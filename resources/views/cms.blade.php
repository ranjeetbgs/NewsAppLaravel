<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NewsPepper | {{$content->title}}</title>
    <meta name="description" content="{{$content->meta_desc}}">
    <meta name="keywords" content="{{$content->meta_keywords}}">
    <meta name="author" content="">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <div class="max-w-4xl mx-auto p-6 sm:p-10">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-8">{{$content->title}}</h1>

    {!! $content->description !!}
  </div>

</body>
</html>
