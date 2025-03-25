<div class="modal fade" id="editAdModal" tabindex="-1" aria-labelledby="editAdLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdLabel">Edit Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAdForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="advertisementId" name="advertisementId">

                    <div class="mb-3">
                        <label for="editAdImage" class="form-label">Advertisement Images (Min 3 images)</label>
                        <input class="form-control" name="adImage[]" id="updateImage" type="file"
                            accept=".jpg,.jpeg,.png,.avif,.webp" multiple
                            onchange="previewMultipleImages(event, 'imagePreviewContainer')">
                        <small class="text-muted">Please select at least 3 images</small>

                        <!-- Image preview container -->
                        <div id="imagePreviewContainer" class="row mt-3 gap-2">
                            <!-- Preview images will be displayed here -->
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateButton">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Preview multiple images
    function previewMultipleImages(event, containerId) {
        const input = event.target;
        const container = document.getElementById(containerId);

        // Clear previous previews
        container.innerHTML = '';

        // Check if at least 3 files are selected
        if (input.files.length < 3) {
            // Show warning
            Swal.fire({
                icon: 'warning',
                title: 'Minimum 3 images required',
                text: 'Please select at least 3 images.'
            });

            // Clear the input to force user to select again
            input.value = '';
            return;
        }

        // Loop through all selected files
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const reader = new FileReader();

            // Create preview element
            const previewCol = document.createElement('div');
            previewCol.className = 'col-md-3 mb-2 position-relative';

            const previewImg = document.createElement('img');
            previewImg.className = 'img-preview img-fluid rounded';
            previewImg.style.height = '150px';
            previewImg.style.objectFit = 'cover';

            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
            removeBtn.innerHTML = '&times;';
            removeBtn.type = 'button';
            removeBtn.dataset.index = i;
            removeBtn.onclick = function() {
                // We can't directly remove files from FileList,
                // so we'll just hide this preview and handle on submit
                this.parentElement.style.display = 'none';
                this.parentElement.classList.add('removed-image');
            };

            // Read and set image
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Append elements
            previewCol.appendChild(previewImg);
            previewCol.appendChild(removeBtn);
            container.appendChild(previewCol);
        }
    }

    // Parse image paths from database
    function parseImagePaths(imageString) {
        if (!imageString) return [];

        // Check if it's already an array
        if (Array.isArray(imageString)) {
            return imageString;
        }

        // If it's a comma-separated string
        if (typeof imageString === 'string' && imageString.includes(',')) {
            return imageString.split(',').map(img => img.trim());
        }

        // Single string path
        return [imageString];
    }

    // Display existing images from database
    function displayExistingImages(images) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';

        // Parse image paths if needed
        const imagePaths = parseImagePaths(images);

        // Loop through and display each image
        imagePaths.forEach((imagePath, index) => {
            const previewCol = document.createElement('div');
            previewCol.className = 'col-md-3 mb-2 position-relative';
            previewCol.dataset.imageId = index;
            previewCol.dataset.imagePath = imagePath;

            const previewImg = document.createElement('img');
            previewImg.className = 'img-preview img-fluid rounded';
            previewImg.style.height = '150px';
            previewImg.style.objectFit = 'cover';

            // Fix path construction - use the full storage path
            previewImg.src = '/storage/' + imagePath.replace(/\\/g, '/');

            // Add an error handler to display broken image info
            previewImg.onerror = function() {
                console.error('Failed to load image: ' + previewImg.src);
                this.src = 'https://via.placeholder.com/150?text=Image+Not+Found';
                this.style.border = '1px solid red';
            };

            // Remove button for existing images
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
            removeBtn.innerHTML = '&times;';
            removeBtn.type = 'button';
            removeBtn.dataset.imageIndex = index;
            removeBtn.onclick = function() {
                // Mark for deletion and hide
                const imagePath = this.parentElement.dataset.imagePath;
                this.parentElement.style.display = 'none';
                this.parentElement.classList.add('removed-image');

                // Add hidden input to track deleted images
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_images[]';
                deleteInput.value = imagePath;
                document.getElementById('editAdForm').appendChild(deleteInput);
            };

            previewCol.appendChild(previewImg);
            previewCol.appendChild(removeBtn);
            container.appendChild(previewCol);
        });
    }

    // Form validation before submit
    document.getElementById('editAdForm').addEventListener('submit', function(e) {
        // Count visible (non-removed) images - both existing and new
        const visibleImages = document.querySelectorAll('#imagePreviewContainer > div:not(.removed-image)')
            .length;
        const newImagesInput = document.getElementById('updateImage');
        const newImagesCount = newImagesInput.files ? newImagesInput.files.length : 0;

        // Check if we have at least 3 images total (existing + new)
        if (visibleImages < 3) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Minimum 3 images required',
                text: 'Please ensure at least 3 images are uploaded.'
            });
        }
    });

    // Get data for editing
    $(document).ready(function() {
        $('#editAdModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var advertisementId = button.data('id');

            // Call data from server
            $.ajax({
                url: "/admin/edit/" + advertisementId + "/advertisement",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#advertisementId').val(data.id);

                    // Display existing images based on what format they're returned in
                    if (data.images && Array.isArray(data.images)) {
                        // Array of image objects
                        displayExistingImages(data.images.map(img => img.path || img));
                    } else if (data.images) {
                        // Images as a string or object
                        displayExistingImages(data.images);
                    } else if (data.adImage) {
                        // The original field name from your code
                        displayExistingImages(data.adImage);
                    } else {
                        console.warn('No images found in response data');
                    }

                    // Update form action
                    $('#editAdForm').attr('action', "/admin/update/" + advertisementId +
                        "/advertisement");
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + " - " + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch advertisement data'
                    });
                }
            });
        });
    });


    //SAVE DB
    $(document).ready(function() {
        $('#editAdForm').on('submit', function(event) {
            event.preventDefault(); // Mencegah reload halaman

            var formData = new FormData(this); // Ambil data dari form
            var advertisementId = $('#advertisementId').val(); // Ambil ID yang akan diedit

            $.ajax({
                url: "/admin/update/" + advertisementId + "/advertisement", // Endpoint update
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('.btn-primary').prop('disabled', true).text('Updating...');
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Advertisement updated successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#editAdModal').modal('hide'); // Tutup modal
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
