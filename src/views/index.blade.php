<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Webisan</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-light bg-faded">
            <h4 class="hidden-sm-up">
                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbar-header" aria-controls="navbar-header" aria-expanded="false" aria-label="Toggle navigation"></button>
                Webisan
            </h4>
            <div class="navbar-toggleable-xs collapse" id="navbar-header" aria-expanded="false">
                <ul class="nav navbar-nav">
                    <li class="nav-item hidden-xs-down"><a href="#" class="nav-link">Webisan</a></li>
                    @if (isset($commandsMenu))
                        @foreach ($commandsMenu as $command)
                            @if (str_contains($command["name"],":*"))
                                <li class="nav-item @if ($currentOption==$command["link"]) active @endif">
                                    <a href="{{ action('\Marcop93\Webisan\WebisanController@show',$command["link"]) }}" class="nav-link">
                                        {!! $command["title"] !!}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                    <hr class="hidden-sm-up">
                    <li class="nav-item float-sm-right" style="padding-top: 13px!important">
                        <select class="form-control" id="search" style="width:200px;max-width: calc(100vw);"></select>
                    </li>
                    <li class="nav-item float-sm-right @if ($currentOption=="settings") active @endif">
                        <a href="{{ action('\Marcop93\Webisan\WebisanController@settings') }}" class="nav-link @if ($currentOption=="settings") active @endif">
                            Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            @if (session("output") && session("command") && session("input") && session("type"))
                <div class="col-xs-12">
                    <div class="card card-inverse card-{{ session("type") }} command-result">
                        <div class="card-header" data-toggle="collapse" href="#command-result-content" aria-expanded="false" aria-controls="command-result-content">
                            <span class="text-uppercase">
                                {{ session('command') }}
                            </span>
                            @if (session("type")=="danger")
                                failed
                            @else
                                was successful
                            @endif
                        </div>
                        <div class="collapse in" id="command-result-content">
                            <div class="card-block">
                                <pre>{!! session('input') !!}<br>{!! session('output') !!}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="col s12">
                    <div class="card red accent-4">
                        <div class="card-content white-text">
                            <span class="card-title">Whoops !<small> There were some problems with your input.</small></span>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @yield("content")
        </div>
        <style>
            nav {
                margin-bottom: 10px;
            }
            .navbar-nav .nav-item {
                margin: -11px 0!important;
                padding: 7px 5px!important;
            }
            .nav-item.active {
                font-weight: 600;
                background-color: #FFF;
            }
            #command-result-content .card-block {
                padding: 10px 20px;
                background-color: #000;
            }
            #command-result-content .card-block pre {
                color: #fff;
            }
        </style>
        <script>
            $("#search").select2({
                placeholder: "Search",
                language: {
                    inputTooShort: function(args) {
                        if (args.input.length==0)
                            return "Type your query"
                        else
                            return "Your query is too short.";
                    },
                    searching: function() {
                        return "Searching";
                    }
                },
                ajax: {
                    method: "post",
                    url: "{{ action('\Marcop93\Webisan\WebisanController@search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                    link: item.link,
                                }
                            }),
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            }).on("select2:select",function(event) {
                window.location.href = event.params.data.link;
            });
            @if (isset($search) && !empty($search))
                $('html, body').animate({
                    scrollTop: $("[href='#{{ str_replace(":", "_", $search) }}']").offset().top
                }, 2000);
            @endif
        </script>
    </body>
</html>