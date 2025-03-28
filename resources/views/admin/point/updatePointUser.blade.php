<div class="modal fade" id="updatePointModal" tabindex="-1" aria-labelledby="editRewardLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRewardLabel">Add Point User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updatePointForm" method="POST">
                    @csrf
                    {{-- <input type="hidden" id="userId" name="userId"> --}}
                    <input type="hidden" id="userId" name="userId">
                    <!-- Nama Pengguna -->
                    <div class="mb-3">
                        <label for="userName" class="form-label">Username</label>
                        <input type="text" class="form-control" id="userName" disabled>
                    </div>

                    <!-- Poin Saat Ini -->
                    <div class="mb-3">
                        <label for="currentPoint" class="form-label">Current Points</label>
                        <input type="text" class="form-control" id="currentPoint" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="updatePoint" class="form-label">Points to be added</label>
                        <input type="text" class="form-control" name="point" id="updatePoint" required
                            oninput="formatPrice(this)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn text-white" style="background-color: #D67B47ff">Add
                            Points</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    //PRICE FORMAT
    function formatPrice(input) {
        // Hapus semua karakter selain angka
        let value = input.value.replace(/,/g, '');

        // Format ulang dengan koma sebagai pemisah ribuan
        input.value = new Intl.NumberFormat('en-US').format(value);
    }

    //GET DATA TO EDIT
    $(document).ready(function() {
        $('#updatePointModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');
            // console.log(userId);

            // Panggil data dari server
            $.ajax({
                url: "/admin/edit/" + userId + "/point",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    // console.log("Data dari server:", data);
                    $('#userId').val(data.id);
                    $('#userName').val(data.username);
                    $('#currentPoint').val(parseFloat(data.point).toLocaleString('en-US'));

                    // Update action form
                    $('#updatePointForm').attr('action', "/admin/update/" + userId +
                        "/point");
                },
                error: function() {
                    alert('Gagal mengambil data User');
                }
            });
        });

        // Format input saat user mengetik
        $('#updatePoint').on('input', function() {
            formatPrice(this);
        });
    });

    //SAVE DB
    $(document).ready(function() {
        $('#updatePointForm').on('submit', function(event) {
            event.preventDefault(); // Mencegah reload halaman

            let formattedPoints = $('#updatePoint').val().replace(/,/g, '');
            $('#updatePoint').val(formattedPoints);
            var formData = new FormData(this); // Ambil data dari form
            var userId = $('#userId').val();
            // console.log("Sending request for userId:", userId);

            $.ajax({
                url: $(this).attr('action'), // Menggunakan action yang sudah diset pada form
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('.btn-primary').prop('disabled', true).text('Adding...');
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Add points successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#updatePointModal').modal('hide'); // Tutup modal
                        $('#imgPreview').hide();
                        location.reload(); // Refresh halaman agar data diperbarui
                    });
                },
                error: function(xhr) {
                    // error handling code tetap sama
                },
                complete: function() {
                    $('.btn-primary').prop('disabled', false);
                }
            });
        });
    });
</script>
