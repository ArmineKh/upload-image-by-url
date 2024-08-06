<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <!-- Styles -->
        <style>
        </style>
    </head>
    <body class="antialiased">
        <div class="container">
            <div class="row my-3">
                <h3 class="fs-1 text-center">
                    Upload image by url
                </h3>
            </div>

            <div class="row"  id="errorMsg">
                <div class="alert">
                </div>
            </div>

            <div class="row my-3">
                <form id="image_upload_form" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="url" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="https://example.com/path/to/file.jpg">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="resolution" class="form-label">Resolution (width-height)</label>
                            <select class="form-select" id="resolution" name="resolution">
                                <option value="200x200">200x200</option>
                                <option value="300x300">300x300</option>
                                <option value="400x400">400x400</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="text" class="form-label">Text in Image</label>
                            <input type="text" class="form-control" id="watermark_text" name="watermark_text" placeholder="Watermark text">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary ms-auto" id="image_upload_form_btn">Upload</button>
                </form>
            </div>

            <div class="row" id="img-container">
                @if($images)
                    @foreach($images as $image)
                        <div class="col-md-3 border border-secondary rounded p-2 mx-1 my-1">
                            <img src="storage{{$image['path']}}" class="card-img-top" alt="...">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <!-- jQuery JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>


        <script>
            $("#image_upload_form_btn").click(function(e){
                e.preventDefault();
                let form = $('#image_upload_form')[0];
                let data = new FormData(form);

                $.ajax({
                    url: "{{ route('uploadImage') }}",
                    type: "POST",
                    data : data,
                    dataType:"JSON",
                    processData : false,
                    contentType:false,

                    success: function(response) {

                        if (response.errors) {
                            let errorMsg = '';
                            $.each(response.errors, function(field, errors) {
                                $.each(errors, function(index, error) {
                                    errorMsg += error + '<br>';
                                });
                            });
                            $('#errorMsg .alert').addClass('alert-danger').html(errorMsg)

                        } else {
                            $('#errorMsg .alert').addClass('alert-success').html(response.success);
                            let imgs = '';
                            $('#img-container').html('')
                            $.each(response.images, function(field, image) {
                                imgs += `<div class="col-md-3 border border-secondary rounded p-2 mx-1 my-1" >
                                  <img src="storage${image.path}" class="card-img-top" alt="...">
                                </div><br>`;
                            });
                            $('#img-container').html(imgs);
                        }

                    },
                    error: function(xhr, status, error) {
                        $('#errorMsg .alert').addClass('alert-danger').html('An error occurred: ' + error)
                    }

                });

            })
        </script>
    </body>
</html>
