@extends('dashboard.adminDashboard')

@section('title', 'Advertisement Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Advertisement</h2>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body border">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            {{-- <h5 class="card-title mb-0">User List</h5> --}}
                        </div>
                        <hr>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Advertisement Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($advertisements as $index => $ad)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($ad->adImage)
                                                    @php
                                                        $images = explode(', ', $ad->adImage); // Pecah string gambar menjadi array
                                                    @endphp

                                                    @foreach ($images as $image)
                                                        <img src="{{ asset('storage/' . $image) }}" width="100"
                                                            height="50" alt="Advertisement Image">
                                                    @endforeach
                                                @else
                                                    No Image
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="#" class="btn btn-sm" style="background-color: #D67B47ff"
                                                        data-bs-toggle="modal" data-bs-target="#editAdModal"
                                                        data-id="{{ $ad->id }}">
                                                        <i class="bi bi-pencil-square text-white"> Update</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.advertisement.updateAd')
@endsection
