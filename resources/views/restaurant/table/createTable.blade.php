<div class="modal fade" id="addTableModal" tabindex="-1" aria-labelledby="addTableLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTableLabel">Add New Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTableForm">
                    @csrf
                    <div class="mb-3">
                        <label for="tableCapacity" class="form-label">Table Capacity</label>
                        <input type="text" class="form-control" id="tableCapacity" name="tableCapacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="availableTables" class="form-label">Available Tables</label>
                        <input type="text" class="form-control" id="availableTables" name="availableTables" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeModalBtn">Close</button>
                <button type="button" class="btn btn-primary" id="saveTable">Save</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#closeModalBtn').on('click', function() {
            setTimeout(function() {
                location.reload();
            }, 300);
        });
    });
    $(document).ready(function() {
        $('#saveTable').on('click', function() {
            var formData = new FormData();
            formData.append('tableCapacity', $('#tableCapacity').val());
            formData.append('availableTables', $('#availableTables').val());

            $.ajax({
                url: '{{ route('table.store') }}',
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
                        $('#addTableForm')[0].reset();
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
