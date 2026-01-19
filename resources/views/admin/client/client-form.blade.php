@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Client</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Client</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Client</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Client</h4>
                        <a href="{{ route('clients.index') }}" class="btn btn-primary mt-10 d-block text-center">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="client_form">
                        @csrf
                        @if (isset($clientData))
                            @method('PUT')
                        @endif
                        <div class="row mb-1">
                            <label for="client_name" class="required col-form-label col-md-2">Name</label>
                            <div class="form-group col-md-6">
                                <input type="text" name="client_name" id="client_name" class="form-control"
                                    autocomplete="name" placeholder="Enter name"
                                    value="{{ $clientData->client_name ?? '' }}">
                                <span id="client_name_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('client_name') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="contact_person" class="col-form-label col-md-2">Contact Person</label>
                            <div class="form-group col-md-6">
                                <input type="text" name="contact_person" id="contact_person" class="form-control"
                                    autocomplete="name" placeholder="Enter contact person"
                                    value="{{ $clientData->contact_person ?? '' }}">
                                <span id="contact_person_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('contact_person') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="email" class="col-form-label col-md-2">Email</label>
                            <div class="form-group col-md-6">
                                <input type="email" name="email" id="email" class="form-control"
                                    autocomplete="email" placeholder="Enter email" value="{{ $clientData->email ?? '' }}">
                                <span id="email_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="phone" class="col-form-label col-md-2">Phone</label>
                            <div class="form-group col-md-6">
                                <input type="phone" name="phone" id="phone" class="form-control"
                                    autocomplete="phone" placeholder="Enter phone" value="{{ $clientData->phone ?? '' }}">
                                <span id="phone_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('phone') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1 align-items-center">
                            <label for="address" class="col-md-2 col-form-label">Address</label>
                            <div class="form-group col-md-6">
                                <textarea rows="2" name="address" id="address" class="form-control">{{ $clientData->address ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-1 align-items-center">
                            <label for="status" class="col-md-2 col-form-label">Status</label>
                            <div class="form-group col-md-6">
                                <select name="status" id="status" class="form-select">
                                    <option value="1"
                                        {{ (isset($clientData) && $clientData->status == '1') || !isset($clientData) ? 'selected' : 'selected' }}>
                                        Active
                                    </option>
                                    <option value="0"
                                        {{ isset($clientData) && $clientData->status == '0' ? 'selected' : '' }}>
                                        Deactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <button type="button" id="client_form_button" class="btn btn-primary float-end"
                            data-url="{{ isset($clientData) ? route('clients.update', $clientData->id) : route('clients.store') }}">
                            {{ isset($clientData) ? 'Update' : 'Submit' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('clients.index') }}";
    </script>
    <script src="{{ asset('admin/assets/js/custom/client.js') }}"></script>
@endsection