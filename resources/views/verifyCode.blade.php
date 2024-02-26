<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    <div class="container">
        <h1>Verify code</h1>
    <form method="POST" action="{{$signedUrl}}">
        @csrf
        <div class="mb-3">
            <label for="exampleInputSms" class="form-label">Code Verify</label>
            <input type="number" id="code" class="form-control" id="exampleInputSms" aria-describedby="smsVerify" required name="code">
            <div id="smsVerify" class="form-text">Enter the code we send you in your email</div>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
    </form>
</div>
</body>
</html>