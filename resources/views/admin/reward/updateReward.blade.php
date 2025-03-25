<div class="modal fade" id="editRewardModal" tabindex="-1" aria-labelledby="editRewardLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRewardLabel">Edit Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRewardForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="rewardId" name="rewardId">

                    <div class="mb-3">
                        <label for="editRewardName" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" id="editRewardName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editRewardImage" class="form-label">Image</label>
                        <input class="form-control" name="image" id="updateImage" type="file"
                            accept=".jpg,.jpeg,.png" onchange="previewImage(event, 'UpdateImgPreview')">
                        <img src="" id="UpdateImgPreview" class="img-preview img-fluid d-block mt-3 col-sm-4"
                            style="display: none;">
                    </div>
                    <div class="mb-3">
                        <label for="editRewardCategory" class="form-label">Category</label>
                        <input type="text" class="form-control" id="editRewardCategory" name="category">
                    </div>
                    <div class="mb-3">
                        <label for="editRewardStock" class="form-label">Stock</label>
                        <input type="text" class="form-control" id="editRewardStock" name="stock">
                    </div>
                    <div class="mb-3">
                        <label for="editPointPrice" class="form-label">Points</label>
                        <input type="text" class="form-control" name="points" id="editPointPrice" required
                            oninput="formatPrice(this)">
                    </div>
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Is Active</label>
                        <select class="form-control" name="is_active" id="is_active" required>
                            <option value=1>Yes</option>
                            <option value=0>No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editDescription"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
    //PREVIEW IMAGE
    function previewImage(event, previewId) {
        const input = event.target; // Ambil elemen input file
        const imgPreview = document.getElementById(previewId); // Pilih elemen img berdasarkan ID

        if (input.files && input.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(input.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.style.display = 'block';
                imgPreview.src = oFREvent.target.result;
            };
        }
    }



    //GET DATA TO EDIT
    $(document).ready(function() {
        $('#editRewardModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var rewardId = button.data('id');

            // Panggil data dari server
            $.ajax({
                url: "/reward/" + rewardId + "/edit",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    // console.log(data);
                    $('#rewardId').val(data.id);
                    $('#editRewardName').val(data.name);
                    $('#editPointPrice').val(parseFloat(data.points).toLocaleString(
                        'en-US'));
                    $("#is_active option").removeAttr('selected');
                    $("#is_active option[value='" + (data.is_active ? "1" : "0") + "']")
                        .attr('selected', 'selected');
                    if (data.is_active == 0) {
                        $("#is_active").val("0");
                    } else {
                        $("#is_active").val("1");
                    }
                    $('#editDescription').val(data.description);

                    // Menyesuaikan value kategori yang sesuai dalam dropdown
                    $('#editRewardCategory').val(data.category);

                    $('#editRewardStock').val(data.stock);

                    if (data.image) {
                        $('.img-preview').attr('src', '/storage/' + data.image);
                    } else {
                        $('.img-preview').attr('src', '');
                    }

                    // Update action form
                    $('#editRewardForm').attr('action', "/reward/" + rewardId + "/update");
                },
                error: function() {
                    alert('Gagal mengambil data reward');
                }
            });
        });

        // Format input saat user mengetik
        $('#editPointPrice').on('input', function() {
            formatPrice(this);
        });
    });

    //SAVE DB
    $(document).ready(function() {
        $('#editRewardForm').on('submit', function(event) {
            event.preventDefault(); // Mencegah reload halaman

            let formattedPoints = $('#editPointPrice').val().replace(/,/g, '');
            $('#editPointPrice').val(formattedPoints);
            var formData = new FormData(this); // Ambil data dari form
            var rewardId = $('#rewardId').val(); // Ambil ID reward yang akan diedit

            $.ajax({
                url: "/reward/" + rewardId + "/update", // Endpoint update
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('.btn-primary').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Reward updated successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#editRewardModal').modal('hide'); // Tutup modal
                        $('#imgPreview').hide();
                        location.reload(); // Refresh halaman agar data diperbarui
                    });
                },
                error: function(xhr) {
                    let errorMessage = "An error occurred.";

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = "";

                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorList += message + "<br>";
                            });
                        });

                        errorMessage = errorList;
                    }

                    Swal.fire({
                        title: "Error!",
                        html: errorMessage, // Gunakan "html" agar pesan error bisa menampilkan <br>
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                },
                complete: function() {
                    $('.btn-primary').prop('disabled', false).text('Update');
                }
            });
        });
    });
</script>
