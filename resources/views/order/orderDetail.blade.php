<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('asset/kulinerinLogo.png') }}" type="image/png">
    <title>Order Detail</title>
    {{-- <script src="script.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #DECEB0ff;
            min-height: 100vh;
        }

        .back-button {
            color: #000;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1rem;
            padding: 10px 20px;
            background-color: #D67B47ff;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            cursor: pointer;
        }

        .back-button i {
            margin-right: 8px;
        }

        /* Hover effect */
        .back-button:hover {
            background-color: rgb(237, 131, 70);
            /* transform: translateY(-2px);  */
        }

        /* Active effect when clicked */
        .back-button:active {
            background-color: rgb(194, 78, 11);
            /* transform: translateY(2px);  */
        }

        /* .back-button:hover {
            color: rgb(159, 105, 74);
        } */
        .back-button i {
            margin-right: 0.5rem;
        }

        .main-container {
            padding: 2rem;
            background-color: #DECEB0ff;
            min-height: 100vh;

        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 500;
            /* margin-bottom: 1.5rem; */
        }

        .line-content {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            /* padding: 0rem 1rem 1rem 1rem; */
        }

        .reservation-box {
            padding: 0rem 1rem 1rem 1rem;
            /* margin-bottom: rem; */
        }

        .menu-box {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 1rem;

        }

        .menu-table {
            margin-bottom: 0;
            background-color: #DECEB0ff
        }

        .menu-table th {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            background-color: #DECEB0ff;
        }

        .menu-table td {
            padding: 1rem;
            background-color: #DECEB0ff;
        }

        .total-row {
            border-top: 1px solid #dee2e6;
        }

        .main-content {
            flex: 1;
            margin-right: 1.5rem;
        }

        .payment-sidebar {
            width: 40%;
            /* height: 100%; */
            /* height: 500px; */
        }

        .total-amount {
            font-size: 2rem;
            text-align: center;
            padding: 1.5rem;
            /* background-color: #fff; */
            background-color: #DECEB0ff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .payment-method {
            /* background-color: #fff; */
            background-color: #DECEB0ff;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .pay-button {
            width: 100%;
            padding: 1rem;
            font-size: 1.25rem;
            background-color: #D67B47ff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #qris-image {
            width: 250px !important;
            height: 250px !important;
            object-fit: cover;
        }

        #qris-container {
            width: auto !important;
            max-width: 100% !important;
            text-align: center;
        }

        @media (max-width: 768px) {
            .order-content {
                flex-direction: column;
            }

            .main-content {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }

            .payment-sidebar {
                width: 100%;
            }

            .main-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <button class="back-button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i>
            Back
        </button>
        <div class="border border-dark rounded">
            <h1 class="section-title text-center" style="padding: 1rem 1rem 0.5rem 1rem;">Order detail</h1>
            {{-- <hr class="border border-dark rounded"> --}}
            <hr style="border: 1px solid black;">
            <div class="d-flex order-content" style="padding: 0rem 1rem 0.5rem 1rem;">
                <div class="main-content">
                    <div class="line-content" style="margin-bottom: 1rem;">
                        <h2 class="h5 mb-1" style="padding: 1rem 1rem 0rem 1rem;">Table</h2>
                        <hr style="color: #FFF">
                        <div class="reservation-box">
                            <p class="mb-2">You are making a reservation for</p>
                            <p class="mb-2">{{ $guestInfo }} guest(s)</p>
                            <p class="mb-2">At {!! $restaurantName !!} ({{ $restaurantCity }})</p>
                            <p class="mb-2">On {{ $reservationDate }}, {{ $reservationTime }} WIB</p>
                        </div>

                    </div>

                    <!-- orderdetail.blade.php -->
                    <div class="menu-box">
                        <table class="table menu-table">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th style="width: 100px;" class="text-center">Qty</th>
                                    <th style="width: 150px;" class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderData as $item)
                                    <tr>
                                        <td>{{ $item['menuName'] }}</td>
                                        <td class="text-center">{{ $item['qty'] }}</td>
                                        <td class="text-end">{{ number_format($item['priceTotal']) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-end"><strong>{{ number_format($grandTotal) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="payment-sidebar">
                    <h4 class="text-center p-1">Payment Summary</h4>
                    <div class="total-amount">
                        {{ number_format($grandTotal) }}
                    </div>

                    <div class="payment-method d-flex justify-content-between align-items-center">
                        <div class="col-4">
                            <span>Payment Method</span>
                        </div>
                        <div class="col-4 text-center">
                            <span>QRIS</span>
                        </div>
                    </div>

                    <button class="pay-button col-4" onclick="generateQris()" id="generate-qris">
                        Pay Now
                    </button>

                    <div id="qris-container" style="margin-top: 20px; text-align: center;">
                        <p id="loading-text" style="display: none; font-size: 16px; color: #555;">Generating QR Code...
                        </p>
                        <div id="loading-spinner" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                        <div id="qris-container2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // function saveMenuQuantities() {
    //     const quantities = {};
    //     document.querySelectorAll('.quantity-value').forEach(input => {
    //         if (input.value && parseInt(input.value) > 0) {
    //             const menuId = input.id.split('_')[0];
    //             quantities[menuId] = parseInt(input.value);
    //         }
    //     });
    //     localStorage.setItem('menuQuantities', JSON.stringify(quantities));
    // }

    function goBack() {
        localStorage.setItem('backPressed', 'true');

        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = "/";
        }
    }


    function generateQris() {
        let amount = "{{ $grandTotal }}";

        document.getElementById("loading-text").style.display = "block";
        document.getElementById("loading-spinner").style.display = "block";
        // document.getElementById("qris-placeholder").style.display = "none";
        document.getElementById("generate-qris").disabled = true;

        fetch("{{ route('generateQris') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    amount: amount
                })
            })
            .then(response => {
                document.getElementById("loading-text").style.display = "none";
                document.getElementById("loading-spinner").style.display = "none";
                return response.json();
            })
            .then(data => {
                // console.log(data);
                if (data.responseCode != "2004700") {
                    throw data;
                } else {
                    document.getElementById("generate-qris").remove();
                    const image = document.createElement("img");
                    image.setAttribute("id", "qris-image");
                    image.setAttribute("src", data.qrUrl);
                    document.getElementById("qris-container").appendChild(image);

                    checkPaymentStatus(data.partnerReferenceNo, data.externalId);
                    // Start countdown
                    startCountdown(data.validityPeriod);
                }
            })
            .catch(error => {
                console.log(error)
                document.getElementById("generate-qris").disabled = false;
                document.getElementById("qris-container").innerHTML =
                    "<p style='color: red;'>Failed to generate QR Code.</p>";
            });
    }

    var countInterval;

    function startCountdown(validityPeriod) {
        function updateCountdown() {
            var timeDiff = new Date(validityPeriod).getTime() - new Date().getTime();

            if (timeDiff <= 0) {
                clearInterval(countInterval);
                document.getElementById("qris-container2").innerHTML = `<p>QRIS Has Expired</p>`;
                return;
            }

            var minutes = Math.floor((timeDiff / 1000 / 60) % 60).toString().padStart(2, '0');
            var seconds = Math.floor((timeDiff / 1000) % 60).toString().padStart(2, '0');

            document.getElementById("qris-container2").innerHTML = `<p>QRIS Expired In: ${minutes}:${seconds}</p>`;
        }

        // Run immediately and set interval
        updateCountdown();
        countInterval = setInterval(updateCountdown, 1000);
    }



    function checkPaymentStatus(partnerReferenceNo, externalId) {
        let interval = setInterval(() => {
            fetch("{{ route('checkStatus') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        partnerReferenceNo: partnerReferenceNo,
                        externalId: externalId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // console.log("Payment Status:", data);
                    if (data.latestTransactionStatus != "03") {
                        clearInterval(interval);
                        clearInterval(countInterval);
                        switch (data.latestTransactionStatus) {
                            case "00":
                                // alert("Payment Succesful");
                                saveReservation();
                                break;

                            case "08":
                                alert("QRIS Expired");
                                window.location.href = "/dashboardCustomer";
                                break;
                        }
                    }
                })
                .catch(error => console.error("Error checking payment status:", error));
        }, 10000);
    }

    function saveReservation() {
        const rawDate = "{{ $reservationDate }}";
        const date = new Date(rawDate);
        const formattedDate = date.toLocaleDateString('en-CA');

        // console.log(formattedDate);
        // Prepare reservation data
        const reservationData = {
            guest: "{{ $guestInfo }}",
            tableType: "{{ $areaInfo }}",
            restaurantName: "{!! $restaurantName !!}",
            restaurantCity: "{{ $restaurantCity }}",
            // reservationDate: "{{ $reservationDate }}",
            reservationDate: formattedDate,
            reservationTime: "{{ $reservationTime }}",
            reservationStatus: "On Going",
            menuData: "{{ $orderDataJson }}",
            priceTotal: "{{ $grandTotal }}",
            restaurantId: "{{ $restaurantId }}"
        };

        // Send data to Laravel API
        fetch("{{ route('booking') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify(reservationData),
            })
            .then(response => response.json())
            .then(data => {
                document.cookie = "success=Reservation Success! Booking Code: " + data.code + ";max-age=1;path=/"
                window.location.href = "/dashboardCustomer";
            })
            .catch(error => console.error("Error:", error));
    }
</script>

</html>
