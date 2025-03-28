<div class="modal fade" id="addRewardModal" tabindex="-1" aria-labelledby="addRewardLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRewardLabel">Add New Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRewardForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input class="form-control" name="image" id="image" type="file"
                            onchange="previewImage(event, 'imgPreview')" accept=".jpg,.jpeg,.png" required>
                        <img id="imgPreview" class="img-preview mt-2" src="" alt="Image Preview"
                            style="display: none; max-width: 200px; height: auto; border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category">
                    </div>
                    <div class="mb-3">
                        <label for="editRewardStock" class="form-label">Stock</label>
                        <input type="text" class="form-control" id="stock" name="stock">
                    </div>
                    <div class="mb-3">
                        <label for="points" class="form-label">Points</label>
                        <input type="text" class="form-control" id="points" name="points" required
                            oninput="formatPrice(this)">
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label">Is Active</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn text-white" style="background-color: #D67B47ff"
                    id="saveReward">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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


    //PRICE FORMAT
    function formatPrice(input) {
        // Hapus semua karakter selain angka
        let value = input.value.replace(/,/g, '');

        // Format ulang dengan koma sebagai pemisah ribuan
        input.value = new Intl.NumberFormat('en-US').format(value);
    }


    $(document).ready(function() {
        $('#saveReward').on('click', function() {
            var formData = new FormData();
            //#name diambil dari id pada field form
            formData.append('name', $('#name').val());
            formData.append('category', $('#category').val());
            formData.append('stock', $('#stock').val());
            // formData.append('points', $('#points').val());
            formData.append('points', $('#points').val().replace(/,/g, ''));
            formData.append('image', $('#image')[0].files[0]);
            formData.append('is_active', $('#is_active').val());
            formData.append('description', $('#description').val());

            $.ajax({
                url: '{{ route('reward.store') }}',
                type: 'POST',
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
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#addMenuModal').modal('hide');
                        $('#addRewardForm')[0].reset();
                        $('#imgPreview').hide();
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    let errorMessage = "Terjadi kesalahan.";

                    if (xhr.status === 422) {
                        let errors = JSON.parse(xhr.responseText).errors;
                        let errorList = [];

                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorList.push(message);
                            });
                        });

                        errorMessage = errorList.join(
                            "<br>"); // Gunakan <br> agar tampil dalam beberapa baris
                    } else if (xhr.status === 500) {
                        errorMessage = JSON.parse(xhr.responseText).error;
                    }

                    Swal.fire({
                        title: "Error!",
                        html: errorMessage, // Gunakan "html" agar <br> bisa diproses
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                },
                complete: function() {
                    $('.btn-primary').prop('disabled', false).text('Save');
                }
            });
        });
    });
</script>
