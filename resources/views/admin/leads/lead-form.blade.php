@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12">

            <div class="card custom-card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        {{ isset($leadData) ? 'Edit Lead' : 'Create Lead' }}
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @isset($leadData)
                            <button class="btn btn-sm btn-info view-followups"
                                data-url="{{ route('admin.leads.followups.list', ['lead' => $leadData->id]) }}"
                                data-lead-id="{{ $leadData->id }}" title="View Follow Ups">
                                <i class="fas fa-comments me-1"></i> Follow Ups
                            </button>
                        @endisset

                        <a href="{{ route('lead.index') }}" class="btn btn-sm btn-primary">
                            Back
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- MAIN FORM --}}
                    <form id="lead_form">
                        @csrf
                        @isset($leadData)
                            @method('PUT')
                        @endisset

                        <div class="row">

                            {{-- Objective --}}
                            {{-- <div class="col-md-6 mb-3">
                                <label>Objective</label><span class="text-danger">*</span>
                                <select name="objective_manager_id" class="form-control select2">
                                    <option value="">Select Objective</option>

                                    @foreach ($objectives as $objective)
                                        <option value="{{ $objective->id }}" @selected(old('objective_manager_id', $leadData->objective_manager_id ?? null) == $objective->id)>
                                            {{ $objective->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="objective_manager_id_error"></small>
                            </div> --}}

                            {{-- Client --}}
                            <div class="col-md-6 mb-3">
                                <label>Client</label>
                                <select name="client_id" id="client_id" class="form-control select2"
                                    onChange="setClientInfo(this)">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-name="{{ $client->client_name }}"
                                            data-phone="{{ $client->phone }}" data-email="{{ $client->email }}"
                                            {{ isset($leadData) && $leadData->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="client_id_error"></small>
                            </div>

                            {{-- Name --}}
                            <div class="col-md-4 mb-3">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ $leadData->name ?? '' }}">
                                <small class="text-danger" id="name_error"></small>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-4 mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone"
                                    value="{{ $leadData->phone ?? '' }}">
                                <small class="text-danger" id="phone_error"></small>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-4 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    value="{{ $leadData->email ?? '' }}">
                                <small class="text-danger" id="email_error"></small>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4 mb-3">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="new"
                                        {{ isset($leadData) && $leadData->status == 'new' ? 'selected' : '' }}>
                                        New</option>
                                    <option value="contacted"
                                        {{ isset($leadData) && $leadData->status == 'contacted' ? 'selected' : '' }}>
                                        Contacted
                                    </option>
                                    <option value="converted"
                                        {{ isset($leadData) && $leadData->status == 'converted' ? 'selected' : '' }}>
                                        Converted
                                    </option>
                                    <option value="lost"
                                        {{ isset($leadData) && $leadData->status == 'lost' ? 'selected' : '' }}>
                                        Lost</option>
                                </select>
                            </div>

                            {{-- Note --}}
                            <div class="col-md-8 mb-3">
                                <label>Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3">{{ $leadData->note ?? '' }}</textarea>
                            </div>

                        </div>

                        {{-- SUBMIT --}}
                        <div class="text-end mt-4">
                            @if (isset($leadData))
                                <button type="submit" class="btn btn-primary" id="lead_form_button"
                                    data-url="{{ route('lead.update', $leadData->id) }}">
                                    Update
                                </button>
                            @else
                                <button type="submit" id="lead_form_button" class="btn btn-primary"
                                    data-url="{{ route('lead.store') }}">
                                    Submit
                                </button>
                            @endif
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
    @include('admin.leads.followups-show')
@endsection

@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('lead.index') }}";
    </script>

    <script src="{{ asset('admin/assets/js/custom/leads.js') }}"></script>
@endsection
