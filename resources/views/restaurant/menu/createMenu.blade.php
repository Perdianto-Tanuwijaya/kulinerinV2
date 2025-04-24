<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuLabel">Add New Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMenuForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="menuName" class="form-label">Menu Name</label>
                        <input type="text" class="form-control" id="menuName" name="menuName" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Menu Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="" disabled selected style="text-align: center">Select Category</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Beverages">Beverages</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="menuPrice" class="form-label">Menu Price</label>
                        <input type="text" class="form-control" id="menuPrice" name="menuPrice" required
                            oninput="formatPrice(this)">
                    </div>
                    <div class="mb-3">
                        <label for="menuImage" class="form-label">Menu Image</label>
                        <input class="form-control" name="menuImage" id="image" type="file"
                            onchange="previewImage(event, 'imgPreview')" accept=".jpg,.jpeg,.png" required>
                        <img id="imgPreview" class="img-preview mt-2" src="" alt="Image Preview"
                            style="display: none; max-width: 200px; height: auto; border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label for="isAvailable" class="form-label">Is Available</label>
                        <select class="form-control" id="isAvailable" name="isAvailable" required>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Menu Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveMenu">Save</button>
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
        $('#saveMenu').on('click', function() {
            var formData = new FormData();
            formData.append('menuName', $('#menuName').val());
            formData.append('category', $('#category').val());
            formData.append('menuPrice', $('#menuPrice').val());
            formData.append('menuImage', $('#image')[0].files[0]);
            formData.append('isAvailable', $('#isAvailable').val());
            formData.append('description', $('#description').val());

            $.ajax({
                url: '{{ route('menu.store') }}',
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
                        $('#addMenuForm')[0].reset();
                        $('#imgPreview').hide();
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    let errorMessage = "Something went wrong.";

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
