<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SwaggerController extends Controller
{
    public function index(): View
    {
        return view('swagger.index');
    }

    public function json(): JsonResponse
    {
        return response()->json([
            'openapi' => '3.0.3',
            'info' => [
                'title' => config('app.name', 'Money Market') . ' API',
                'version' => '1.0.0',
                'description' => 'Swagger documentation for the Money Market employee API.',
            ],
            'servers' => [
                ['url' => url('/api'), 'description' => 'Current application API server'],
            ],
            'tags' => [
                ['name' => 'Auth'],
                ['name' => 'Master Data'],
                ['name' => 'Leads'],
                ['name' => 'CRM'],
                ['name' => 'RTO'],
                ['name' => 'Insurance Leads'],
                ['name' => 'Referral Logs'],
            ],
            'paths' => $this->paths(),
            'components' => $this->components(),
        ]);
    }

    private function paths(): array
    {
        return [
            '/login' => [
                'post' => $this->operation('Auth', 'Employee API login', 'Creates a Sanctum bearer token.', null, [
                    'employee_id' => ['type' => 'string', 'example' => 'EMP001'],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'employee@example.com'],
                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'password'],
                ], false, ['200' => 'Login successful', '403' => 'Admin login blocked', '422' => 'Validation error']),
            ],
            '/forgot-password' => [
                'post' => $this->operation('Auth', 'Forgot password', 'Sends a password reset link to an employee email address.', null, [
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'employee@example.com'],
                ], false, ['200' => 'Reset link sent', '403' => 'Admin reset blocked', '422' => 'Validation error']),
            ],
            '/reset-password' => [
                'post' => $this->operation('Auth', 'Reset password', 'Resets an employee password using a password reset token.', null, [
                    'token' => ['type' => 'string'],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'employee@example.com'],
                    'password' => ['type' => 'string', 'format' => 'password', 'minLength' => 8],
                    'password_confirmation' => ['type' => 'string', 'format' => 'password', 'minLength' => 8],
                ], false, ['200' => 'Password reset', '403' => 'Admin reset blocked', '422' => 'Validation error']),
            ],
            '/user' => [
                'get' => $this->operation('Auth', 'Authenticated user', 'Returns the current authenticated user.'),
            ],
            '/logout' => [
                'post' => $this->operation('Auth', 'Logout', 'Deletes the current Sanctum access token.'),
            ],
            '/firms' => [
                'get' => $this->operation('Master Data', 'Active firms', 'Returns active firms.'),
            ],
            '/states' => [
                'get' => $this->operation('Master Data', 'States', 'Returns all states.'),
            ],
            '/cities/{state}' => [
                'get' => $this->operation('Master Data', 'Cities by state', 'Returns cities for a state.', [
                    $this->pathParam('state', 'integer', 'State ID'),
                ]),
            ],
            '/location/{pincode}' => [
                'get' => $this->operation('Master Data', 'Location by pincode', 'Returns city and state for a pincode.', [
                    $this->pathParam('pincode', 'string', 'Pincode'),
                ], null, true, ['200' => 'Location found', '404' => 'Location not found']),
            ],
            '/leads' => [
                'get' => $this->operation('Leads', 'List leads', 'Returns paginated leads visible to the authenticated employee.', [
                    $this->queryParam('status'),
                    $this->queryParam('start_date', 'string', 'date'),
                    $this->queryParam('end_date', 'string', 'date'),
                    $this->queryParam('assigned_to', 'integer'),
                    $this->queryParam('per_page', 'integer'),
                ]),
                'post' => $this->operation('Leads', 'Create lead', 'Creates a lead. Use multipart/form-data when uploading documents.', null, $this->leadSchema(), true, ['201' => 'Lead created', '422' => 'Validation error']),
            ],
            '/leads/{lead}' => [
                'get' => $this->operation('Leads', 'Show lead', 'Returns one lead.', [$this->pathParam('lead', 'integer', 'Lead ID')]),
                'put' => $this->operation('Leads', 'Update lead', 'Updates one lead.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->leadSchema(false), true, ['200' => 'Lead updated', '403' => 'Unauthorized', '422' => 'Validation error']),
            ],
            '/leads/{lead}/status' => [
                'post' => $this->operation('Leads', 'Update lead status', 'Updates lead workflow status.', [$this->pathParam('lead', 'integer', 'Lead ID')], [
                    'action' => ['type' => 'string', 'enum' => ['interested', 'not_interested', 'follow_up', 'qualify', 'proceed_to_bank_assign', 'reject', 'mark_active']],
                    'remark' => ['type' => 'string', 'nullable' => true],
                    'follow_up_date' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'follow_up_time' => ['type' => 'string', 'nullable' => true],
                    'bank_id' => ['type' => 'integer', 'nullable' => true],
                    'bank_branch_id' => ['type' => 'integer', 'nullable' => true],
                    'bank_executive_name' => ['type' => 'string', 'nullable' => true],
                    'new_status' => ['type' => 'string', 'enum' => ['New', 'Prospect'], 'nullable' => true],
                    'not_active' => ['type' => 'boolean', 'nullable' => true],
                ]),
            ],
            '/leads/{lead}/assign-bank' => [
                'post' => $this->operation('Leads', 'Assign bank', 'Assigns a bank and branch to a lead.', [$this->pathParam('lead', 'integer', 'Lead ID')], [
                    'bank_id' => ['type' => 'integer'],
                    'bank_branch_id' => ['type' => 'integer'],
                    'bank_executive_name' => ['type' => 'string', 'nullable' => true],
                ]),
            ],
            '/leads/meta/assignable-users' => [
                'get' => $this->operation('Leads', 'Assignable users', 'Returns users that the authenticated employee can assign leads to.'),
            ],
            '/leads/meta/managers/{user}' => [
                'get' => $this->operation('Leads', 'Managers by user', 'Returns eligible managers for a user.', [$this->pathParam('user', 'integer', 'User ID')]),
            ],
            '/lead-source-persons/search' => [
                'get' => $this->operation('Leads', 'Search lead source persons', 'Searches employees, past leads, and referral persons.', [$this->queryParam('q')]),
            ],
            '/lead-source-persons' => [
                'post' => $this->operation('Leads', 'Create referral person', 'Creates a referral person for lead source selection.', null, [
                    'name' => ['type' => 'string'],
                    'contact_number' => ['type' => 'string', 'nullable' => true],
                    'email' => ['type' => 'string', 'format' => 'email', 'nullable' => true],
                    'address' => ['type' => 'string', 'nullable' => true],
                ], true, ['201' => 'Referral person created']),
            ],
            '/crm' => [
                'get' => $this->operation('CRM', 'List CRM leads', 'Returns CRM leads.'),
            ],
            '/crm/{lead}' => [
                'get' => $this->operation('CRM', 'Show CRM lead', 'Returns CRM details for a lead.', [$this->pathParam('lead', 'integer', 'Lead ID')]),
            ],
            '/crm/{lead}/stage' => [
                'post' => $this->operation('CRM', 'Update CRM stage', 'Updates the CRM stage for a lead.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto' => [
                'get' => $this->operation('RTO', 'List RTO leads', 'Returns RTO leads.'),
                'post' => $this->operation('RTO', 'Create RTO lead', 'Creates an RTO lead.', null, $this->genericPayload()),
            ],
            '/rto/store-insurance-lead' => [
                'post' => $this->operation('RTO', 'Create insurance lead from RTO', 'Creates an insurance lead from RTO flow.', null, $this->genericPayload()),
            ],
            '/rto/{lead}' => [
                'get' => $this->operation('RTO', 'Show RTO lead', 'Returns RTO lead details.', [$this->pathParam('lead', 'integer', 'Lead ID')]),
            ],
            '/rto/{lead}/stage' => [
                'post' => $this->operation('RTO', 'Update RTO stage', 'Updates RTO stage.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/update-vehicle-detail' => [
                'post' => $this->operation('RTO', 'Update vehicle detail', 'Updates vehicle details.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/assign-agent' => [
                'post' => $this->operation('RTO', 'Assign RTO agent', 'Assigns an RTO agent.', [$this->pathParam('lead', 'integer', 'Lead ID')], ['rto_agent_id' => ['type' => 'integer']]),
            ],
            '/rto/{lead}/update-challan' => [
                'post' => $this->operation('RTO', 'Update challan', 'Updates challan details.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/upload-doc' => [
                'post' => $this->operation('RTO', 'Upload RTO document', 'Uploads an RTO document.', [$this->pathParam('lead', 'integer', 'Lead ID')], ['document' => ['type' => 'string', 'format' => 'binary']], true, ['200' => 'Document uploaded'], 'multipart/form-data'),
            ],
            '/rto/{lead}/update-verification' => [
                'post' => $this->operation('RTO', 'Update verification', 'Updates verification details.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/update-approval' => [
                'post' => $this->operation('RTO', 'Update approval', 'Updates approval details.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/rto-done' => [
                'post' => $this->operation('RTO', 'Mark RTO done', 'Marks RTO as completed.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/rto/{lead}/reject' => [
                'post' => $this->operation('RTO', 'Reject RTO lead', 'Rejects an RTO lead.', [$this->pathParam('lead', 'integer', 'Lead ID')], ['remark' => ['type' => 'string']]),
            ],
            '/insurance-leads' => [
                'get' => $this->operation('Insurance Leads', 'List insurance leads', 'Returns insurance leads.'),
                'post' => $this->operation('Insurance Leads', 'Create insurance lead', 'Creates an insurance lead.', null, $this->genericPayload()),
            ],
            '/insurance-leads/expiring' => [
                'get' => $this->operation('Insurance Leads', 'Expiring insurance leads', 'Returns expiring insurance leads.'),
            ],
            '/insurance-leads/{lead}' => [
                'get' => $this->operation('Insurance Leads', 'Show insurance lead', 'Returns one insurance lead.', [$this->pathParam('lead', 'integer', 'Lead ID')]),
            ],
            '/insurance-leads/{lead}/details' => [
                'post' => $this->operation('Insurance Leads', 'Update insurance details', 'Updates insurance lead details.', [$this->pathParam('lead', 'integer', 'Lead ID')], $this->genericPayload()),
            ],
            '/referral-logs' => [
                'get' => $this->operation('Referral Logs', 'List referral logs', 'Returns referral logs.'),
            ],
            '/referral-logs/{referral}/mark-as-paid' => [
                'patch' => $this->operation('Referral Logs', 'Mark referral paid', 'Marks a referral log as paid.', [$this->pathParam('referral', 'integer', 'Referral log ID')]),
            ],
        ];
    }

    private function operation(
        string $tag,
        string $summary,
        string $description,
        ?array $parameters = null,
        ?array $requestProperties = null,
        bool $secured = true,
        array $responses = ['200' => 'Successful response'],
        string $contentType = 'application/json'
    ): array {
        $operation = [
            'tags' => [$tag],
            'summary' => $summary,
            'description' => $description,
            'responses' => $this->responses($responses),
        ];

        if ($parameters) {
            $operation['parameters'] = $parameters;
        }

        if ($requestProperties) {
            $operation['requestBody'] = [
                'required' => true,
                'content' => [
                    $contentType => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => $requestProperties,
                        ],
                    ],
                ],
            ];
        }

        if ($secured) {
            $operation['security'] = [['sanctum' => []]];
        }

        return $operation;
    }

    private function responses(array $responses): array
    {
        $defaultResponses = [
            '401' => 'Unauthenticated',
            '422' => 'Validation error',
        ];

        $items = [];
        foreach ($responses + $defaultResponses as $status => $description) {
            $items[$status] = [
                'description' => $description,
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/ApiResponse'],
                    ],
                ],
            ];
        }

        return $items;
    }

    private function pathParam(string $name, string $type, string $description): array
    {
        return [
            'name' => $name,
            'in' => 'path',
            'required' => true,
            'description' => $description,
            'schema' => ['type' => $type],
        ];
    }

    private function queryParam(string $name, string $type = 'string', ?string $format = null): array
    {
        $schema = ['type' => $type];
        if ($format) {
            $schema['format'] = $format;
        }

        return [
            'name' => $name,
            'in' => 'query',
            'required' => false,
            'schema' => $schema,
        ];
    }

    private function leadSchema(bool $required = true): array
    {
        return [
            'customer_name' => ['type' => 'string', 'example' => 'Rahul Sharma'],
            'contact_number' => ['type' => 'string', 'example' => '9876543210'],
            'father_name' => ['type' => 'string', 'nullable' => true],
            'email' => ['type' => 'string', 'format' => 'email', 'nullable' => true],
            'firm_id' => ['type' => 'integer', 'nullable' => true],
            'residence_type' => ['type' => 'string', 'nullable' => true],
            'current_address_line' => ['type' => 'string', 'nullable' => true],
            'current_address_pincode' => ['type' => 'string', 'nullable' => true],
            'current_address_state' => ['type' => 'integer', 'nullable' => true],
            'current_address_city' => ['type' => 'integer', 'nullable' => true],
            'permanent_address_line' => ['type' => 'string', 'nullable' => true],
            'permanent_address_pincode' => ['type' => 'string', 'nullable' => true],
            'permanent_address_state' => ['type' => 'integer', 'nullable' => true],
            'permanent_address_city' => ['type' => 'integer', 'nullable' => true],
            'lead_type' => ['type' => 'string', 'enum' => ['Loan', 'RTO', 'Insurance'], 'nullable' => !$required],
            'lead_source' => ['type' => 'string', 'nullable' => true],
            'lead_source_person_name' => ['type' => 'string', 'nullable' => true],
            'assigned_to' => ['type' => 'integer', 'nullable' => true],
            'reporting_manager_id' => ['type' => 'integer', 'nullable' => true],
            'premium_amount_collected' => ['type' => 'string', 'enum' => ['Yes', 'No'], 'nullable' => true],
            'premium_payment_type' => ['type' => 'string', 'enum' => ['Cash', 'Cheque', 'Online', 'Bank Transfer'], 'nullable' => true],
            'premium_payment_amount' => ['type' => 'number', 'nullable' => true],
            'premium_payment_date' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
        ];
    }

    private function genericPayload(): array
    {
        return [
            'status' => ['type' => 'string', 'nullable' => true],
            'stage' => ['type' => 'string', 'nullable' => true],
            'remark' => ['type' => 'string', 'nullable' => true],
        ];
    }

    private function components(): array
    {
        return [
            'securitySchemes' => [
                'sanctum' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'Sanctum token',
                ],
            ],
            'schemas' => [
                'ApiResponse' => [
                    'type' => 'object',
                    'additionalProperties' => true,
                ],
            ],
        ];
    }
}
