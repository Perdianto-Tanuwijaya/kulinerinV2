<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu | {{ $restaurants->restaurantName }}</title>
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background-color: #DECEB0ff;
        width: 100%;
        min-height: 100vh;
    }

    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 32px;
    }

    .card img {
        max-height: 150px;
        /* object-fit: cover; */
    }

    .nav-pills .nav-link {
        color: #333;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
    }


    .btn-circle {
        width: 42px;
        height: 42px;
        aspect-ratio: 1/1;
        font-size: 22px;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background-color: #28a745;
        color: white;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    .minus-btn {
        background-color: #dc3545;
    }

    .btn-circle:hover {
        background-color: rgba(40, 167, 69, 0.9);
    }

    .minus-btn:hover {
        background-color: rgba(220, 53, 69, 0.9);
    }

    .btn-circle:active {
        transform: scale(0.95);
    }

    .quantity-value {
        width: 50px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }
</style>
@extends('master.masterCustomer')
@section('content')

    <body>
        <!-- Main Content -->
        <div class="container py-4">
            <div class="text-end">
                <form method="POST" action="{{ route('detailOrder') }}" id="detailOrderForm">
                    @csrf
                </form>
                <button class="btn" style="background-color: #D67B47ff" onclick="detailOrder()">Go to cart</button>
            </div>

            @foreach ($menuItems as $category => $menus)
                <h2 class="mb-4 fs-2" style="padding-bottom:10px">{{ $category }}</h2>
                <div class="row g-4" style="padding-bottom: 2rem">
                    @foreach ($menus as $menu)
                        <div class="col-12 col-sm-6 col-md-4" style="padding-top: 1rem;">
                            <div class="card h-100 d-flex flex-column"
                                style="border-radius: 20px; background-color: #DECEB0ff; border-color: white; overflow: hidden;">
                                <div class="row g-0 h-100">
                                    <div class="col-5 h-100 d-flex">
                                        <img src="{{ asset('storage/' . $menu->menuImage) }}"
                                            class="img-fluid rounded-start"
                                            style="width: 100%; height: 100%; object-fit: cover; min-height: 100%;"
                                            alt="Menu Image">
                                    </div>
                                    <div class="col-7 d-flex flex-column justify-content-between h-100">
                                        <div class="card-body">
                                            <h5 class="card-title" id="menuName">{{ $menu->menuName }}</h5>
                                            <p class="card-text small">{{ $menu->description }}</p>
                                        </div>
                                        <div class="card-footer border-0 px-3 py-2" style="background-color: #DECEB0ff;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span id="menuPrice" class="fw-bold">
                                                    {{ number_format($menu->menuPrice, 0, '.', ',') }}
                                                </span>
                                                <div class="quantity-container d-flex align-items-center">
                                                    <button class="btn btn-circle plus-initial" type="button">+</button>
                                                    <div class="input-group input-group-sm quantity-selector d-none mx-2">
                                                        <button class="btn btn-circle minus-btn" type="button">-</button>
                                                        <input type="text"
                                                            class="form-control text-center quantity-value"
                                                            id="{{ $menu->id }}_qty">
                                                        <button class="btn btn-circle plus-btn" type="button">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Populate hidden input fields
            document.getElementById("hidden-guest-info").value =
                "{{ $guestInfo }} guest(s)";
            document.getElementById("hidden-reservation-info").value =
                "{{ $reservationDate }}, {{ $reservationTime }}";
            document.getElementById("hidden-restaurant-name").value = "{!! $restaurantName !!}";
            document.getElementById("hidden-restaurant-city").value = "{{ $restaurantCity }}";

        });

        function detailOrder() {
            const guestInfo = "{{ $guestInfo }}";
            const bookDate = "{{ $reservationDate }}";
            // console.log(bookDate);

            const reservationDate = new Date(bookDate).toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const reservationTime = "{{ $reservationTime }}";
            const restaurantName = "{!! $restaurantName !!}";
            const restaurantCity = "{{ $restaurantCity }}";
            const restaurantId = "{{ $restaurantId }}";

            let orderData = [];
            let grandTotal = 0;

            // Collect all menu items with their qty, name, and price
            document.querySelectorAll('.card').forEach(function(card) {
                let qtyInput = card.querySelector('.quantity-value');
                let qty = qtyInput ? parseInt(qtyInput.value, 10) : 0;
                let menuName = card.querySelector('#menuName').textContent.trim();
                let menuPrice = card.querySelector('#menuPrice').textContent.replace(/[^0-9.]/g, '');
                let priceTotal = qty * parseFloat(menuPrice);

                if (qty > 0) {
                    orderData.push({
                        qty: qty,
                        menuName: menuName,
                        menuPrice: parseFloat(menuPrice).toFixed(2),
                        priceTotal: priceTotal.toFixed(2)
                    });

                    grandTotal += priceTotal;
                }
            });

            localStorage.setItem('orderData', JSON.stringify(orderData));

            // Validasi: Jika tidak ada menu yang dipilih, tampilkan alert dan hentikan proses
            if (orderData.length === 0) {
                alert("Please select at least one menu item before proceeding to the cart.");
                return;
            }

            const form = document.getElementById('detailOrderForm');

            const odForm = document.createElement('input');
            odForm.type = 'hidden';
            odForm.name = 'orderData';
            odForm.value = JSON.stringify(orderData);
            form.appendChild(odForm);

            const gtForm = document.createElement('input');
            gtForm.type = 'hidden';
            gtForm.name = 'grandTotal';
            gtForm.value = grandTotal;
            form.appendChild(gtForm);

            const giForm = document.createElement('input');
            giForm.type = 'hidden';
            giForm.name = 'guestInfo';
            giForm.value = guestInfo;
            form.appendChild(giForm);

            const rdForm = document.createElement('input');
            rdForm.type = 'hidden';
            rdForm.name = 'reservationDate';
            rdForm.value = reservationDate;
            form.appendChild(rdForm);

            const rtForm = document.createElement('input');
            rtForm.type = 'hidden';
            rtForm.name = 'reservationTime';
            rtForm.value = reservationTime;
            form.appendChild(rtForm);

            const rnForm = document.createElement('input');
            rnForm.type = 'hidden';
            rnForm.name = 'restaurantName';
            rnForm.value = restaurantName;
            form.appendChild(rnForm);

            const rcForm = document.createElement('input');
            rcForm.type = 'hidden';
            rcForm.name = 'restaurantCity';
            rcForm.value = restaurantCity;
            form.appendChild(rcForm);

            const ridForm = document.createElement('input');
            ridForm.type = 'hidden';
            ridForm.name = 'restaurantId';
            ridForm.value = restaurantId;
            form.appendChild(ridForm);

            form.submit();
        }


        // document.addEventListener("DOMContentLoaded", function() {
        //     document.querySelectorAll(".quantity-container").forEach(container => {
        //         const initialPlusBtn = container.querySelector(".plus-initial");
        //         const quantitySelector = container.querySelector(".quantity-selector");
        //         const minusBtn = container.querySelector(".minus-btn");
        //         const plusBtn = container.querySelector(".plus-btn");
        //         const quantityInput = container.querySelector(".quantity-value");
        //         let count = 0;

        //         // Show quantity selector when "+" is clicked
        //         initialPlusBtn.addEventListener("click", function() {
        //             initialPlusBtn.classList.add("d-none");
        //             quantitySelector.classList.remove("d-none");
        //             count++;
        //             quantityInput.value = count;
        //         });

        //         // Increase quantity
        //         plusBtn.addEventListener("click", function() {
        //             count++;
        //             quantityInput.value = count;
        //         });

        //         // Decrease quantity, hide if count goes back to 1
        //         minusBtn.addEventListener("click", function() {
        //             if (count > 1) {
        //                 count--;
        //                 quantityInput.value = count;
        //             } else {
        //                 quantitySelector.classList.add("d-none");
        //                 initialPlusBtn.classList.remove("d-none");
        //                 count = 0; // Reset to 1 when hidden
        //                 quantityInput.value = count;
        //             }
        //         });
        //     });
        // });

        document.addEventListener("DOMContentLoaded", function() {
            // Cek apakah pengguna kembali menggunakan tombol back
            const backPressed = localStorage.getItem('backPressed') === 'true';

            // Jika pengguna kembali dengan tombol back, pulihkan kuantitas
            if (backPressed) {
                // Hapus tanda setelah digunakan
                localStorage.removeItem('backPressed');

                // Ambil data order dari localStorage yang tersimpan saat Go to Cart
                const orderData = JSON.parse(localStorage.getItem('orderData')) || [];

                // Buat mapping menuName ke quantity
                const menuQuantities = {};
                orderData.forEach(item => {
                    menuQuantities[item.menuName] = item.qty;
                });

                // Terapkan kuantitas ke masing-masing menu
                document.querySelectorAll('.card').forEach(card => {
                    const menuName = card.querySelector('#menuName').textContent.trim();
                    const quantityContainer = card.querySelector('.quantity-container');

                    if (quantityContainer && menuQuantities[menuName]) {
                        const initialPlusBtn = quantityContainer.querySelector('.plus-initial');
                        const quantitySelector = quantityContainer.querySelector('.quantity-selector');
                        const quantityInput = quantityContainer.querySelector('.quantity-value');

                        // Tampilkan selector dan atur nilai
                        initialPlusBtn.classList.add('d-none');
                        quantitySelector.classList.remove('d-none');
                        quantityInput.value = menuQuantities[menuName];

                        // Update variabel count pada handler
                        const container = quantityContainer;
                        const minusBtn = container.querySelector(".minus-btn");
                        const plusBtn = container.querySelector(".plus-btn");
                        let count = menuQuantities[menuName];

                        // Rebind event untuk plus/minus
                        plusBtn.onclick = function() {
                            count++;
                            quantityInput.value = count;
                        };

                        minusBtn.onclick = function() {
                            if (count > 1) {
                                count--;
                                quantityInput.value = count;
                            } else {
                                quantitySelector.classList.add("d-none");
                                initialPlusBtn.classList.remove("d-none");
                                count = 0;
                                quantityInput.value = count;
                            }
                        };
                    }
                });
            }

            // Kode event listener asli untuk penambahan/pengurangan tetap ada
            document.querySelectorAll(".quantity-container").forEach(container => {
                const initialPlusBtn = container.querySelector(".plus-initial");
                const quantitySelector = container.querySelector(".quantity-selector");
                const minusBtn = container.querySelector(".minus-btn");
                const plusBtn = container.querySelector(".plus-btn");
                const quantityInput = container.querySelector(".quantity-value");

                // Hanya inisialisasi count jika belum ada nilai sebelumnya
                let count = parseInt(quantityInput.value) || 0;

                // Show quantity selector when "+" is clicked
                initialPlusBtn.addEventListener("click", function() {
                    initialPlusBtn.classList.add("d-none");
                    quantitySelector.classList.remove("d-none");
                    count++;
                    quantityInput.value = count;
                });

                // Increase quantity
                plusBtn.addEventListener("click", function() {
                    count++;
                    quantityInput.value = count;
                });

                // Decrease quantity, hide if count goes back to 1
                minusBtn.addEventListener("click", function() {
                    if (count > 1) {
                        count--;
                        quantityInput.value = count;
                    } else {
                        quantitySelector.classList.add("d-none");
                        initialPlusBtn.classList.remove("d-none");
                        count = 0; // Reset to 0 when hidden
                        quantityInput.value = count;
                    }
                });
            });
        });
    </script>
@endsection

</html>
