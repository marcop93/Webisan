@extends("Webisan::index")

@section("content")
    @if (Session::has('settings'))
        @if (Session::get('settings')=="success")
            <div class="alert alert-success alert-dismissible show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                Settings were successfully saved.
            </div>
        @else
            <div class="alert alert-danger alert-dismissible show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                Failed to save settings.
            </div>
        @endif
    @endif

    {{ Form::open(array('url' => action('\Marcop93\Webisan\WebisanController@settingsSave'))) }}


    <h4>Settings</h4>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="card">
                <div class="card-header h6" >
                    Ignored Commands
                </div>
                <div id="ignored">
                    <div class="card-block">
                        {{ Form::select("ignore[]", $commandsSelect, $commandsSelected, ["multiple"=>true,"style"=>"width:100%;"]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="card">
                <div class="card-header h6">
                    Custom Routes
                </div>
                <div id="custom-routes">
                    <div class="card-block">
                        {{ Form::checkbox("customRoutes", null, $settings["customRoutes"]) }} Use custom routes<br>
                        Make sure to add the Webisan routes to your router file or you're going to lose access.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    {!! Form::submit('Guardar definições',["class"=>"btn btn-primary"]) !!}
    {!! Form::close() !!}
    <hr>
    <div class="card">
        <div class="card-header h6">
            About
        </div>
        <div class="card-block">
            Webisan is A Web Interface for Laravel Artisan.<br>
            Everything is at <a href="https://github.com/marcop93/webisan" target="_blank">Webisan GitHub Page</a>.
        </div>
    </div>
    <script>
        $("select[name='ignore[]']").select2();
    </script>
@endsection