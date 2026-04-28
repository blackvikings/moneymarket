@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @php
        $dashboardFilters = array_filter([
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
        ]);
    @endphp
    <div class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <p>Lead summary by module and assignment flow</p>
        </div>
        <form action="{{ route('home') }}" method="GET" class="dashboard-filter-form">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>
@stop

@section('content')
    <div class="dashboard-grid">
        <div class="dashboard-card dashboard-card-info">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-bag"></i></div>
                <span class="dashboard-card__tag">Overall</span>
            </div>
            <div class="dashboard-card__value">{{ $totalLeads }}</div>
            <div class="dashboard-card__label">Total Leads</div>
            <a href="{{ route(get_route_prefix() . 'leads.index', $dashboardFilters) }}" class="dashboard-card__link">Open leads <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dashboard-card dashboard-card-success">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-stats-bars"></i></div>
                <span class="dashboard-card__tag">Qualified</span>
            </div>
            <div class="dashboard-card__value">{{ $confirmedLeads }}</div>
            <div class="dashboard-card__label">Confirmed Leads</div>
            <a href="{{ route(get_route_prefix() . 'leads.index', array_merge($dashboardFilters, ['status' => 'Lead'])) }}" class="dashboard-card__link">Open leads <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dashboard-card dashboard-card-warning">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-university"></i></div>
                <span class="dashboard-card__tag">Bank flow</span>
            </div>
            <div class="dashboard-card__value">{{ $assignedToBank }}</div>
            <div class="dashboard-card__label">Assigned to Bank</div>
            <a href="{{ route(get_route_prefix() . 'crm.index', $dashboardFilters) }}" class="dashboard-card__link">Open CRM <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dashboard-card dashboard-card-danger dashboard-card-half">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-car"></i></div>
                <span class="dashboard-card__tag">Direct module</span>
            </div>
            <div class="dashboard-card__value">{{ $directRtoLeads }}</div>
            <div class="dashboard-card__label">Direct RTO Leads</div>
            <a href="{{ route(get_route_prefix() . 'rto.index', $dashboardFilters) }}" class="dashboard-card__link">Open RTO leads <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dashboard-card dashboard-card-primary dashboard-card-half">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-shield"></i></div>
                <span class="dashboard-card__tag">Direct module</span>
            </div>
            <div class="dashboard-card__value">{{ $directInsuranceLeads }}</div>
            <div class="dashboard-card__label">Direct Insurance Leads</div>
            <a href="{{ route(get_route_prefix() . 'insurance-leads.index', $dashboardFilters) }}" class="dashboard-card__link">Open insurance leads <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dashboard-card dashboard-card-half" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
            <div class="dashboard-card__top">
                <div class="dashboard-card__icon"><i class="ion ion-alert-circled"></i></div>
                <span class="dashboard-card__tag">Insurance</span>
            </div>
            <div class="dashboard-card__value">{{ $thisMonthExpiringInsuranceLeads }}</div>
            <div class="dashboard-card__label">This Month Expiring Leads</div>
            <a href="{{ route(get_route_prefix() . 'insurance-leads.expiring') }}" class="dashboard-card__link">Open expiring leads <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .dashboard-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .dashboard-header p {
            margin: 0.35rem 0 0;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .dashboard-filter-form {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .dashboard-filter-form .form-control {
            min-width: 170px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }

        .dashboard-card {
            grid-column: span 4;
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            padding: 1.25rem;
            min-height: 175px;
            color: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.14);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.18);
        }

        .dashboard-card-half {
            grid-column: span 6;
        }

        .dashboard-card-info { background: linear-gradient(135deg, #1f9fbf 0%, #1288a6 100%); }
        .dashboard-card-success { background: linear-gradient(135deg, #28a745 0%, #1f8d3a 100%); }
        .dashboard-card-warning { background: linear-gradient(135deg, #f0b429 0%, #d89c00 100%); color: #1f2937; }
        .dashboard-card-danger { background: linear-gradient(135deg, #d83a49 0%, #b92c3a 100%); }
        .dashboard-card-primary { background: linear-gradient(135deg, #1677ff 0%, #0d5bd6 100%); }

        .dashboard-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .dashboard-card__icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.16);
            font-size: 1.1rem;
        }

        .dashboard-card__tag {
            font-size: 0.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            white-space: nowrap;
        }

        .dashboard-card__value {
            font-size: 2.8rem;
            line-height: 1;
            font-weight: 800;
            margin-top: 0.9rem;
        }

        .dashboard-card__label {
            margin-top: 0.35rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .dashboard-card__link {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: inherit;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.92;
        }

        .dashboard-card__link:hover {
            opacity: 1;
            text-decoration: none;
        }

        .dashboard-card-warning .dashboard-card__link,
        .dashboard-card-warning .dashboard-card__tag {
            color: #1f2937;
        }

        @media (max-width: 1199.98px) {
            .dashboard-card,
            .dashboard-card-half {
                grid-column: span 6;
            }
        }

        @media (max-width: 767.98px) {
            .dashboard-card,
            .dashboard-card-half {
                grid-column: span 12;
            }

            .dashboard-header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
@stop
