@extends("Webisan::index")

@section("content")
    <div class="row">
        @if (isset($commandsMenu) && isset($option))
            @foreach ($commandsMenu[$option]["subcommands"] as $command)
                <div class="col-xs-12 col-md-6">
                    <div class="card">
                        <div data-toggle="collapse" href="#{{ $command["selector"] }}" aria-expanded="false" aria-controls="{{ $command["selector"] }}"
                            class="card-header h6 @if (isset($search) && $search==$command["name"]) card-inverse card-info @endif">
                            {{ $command["name"] }}
                            <span class="float-xs-right text-muted" style="font-size: 12px;">
                                {{ $command["description"] }}
                            </span>
                        </div>
                        <div id="{{ $command["selector"] }}" class="collapse @if (isset($search) && $search==$command["name"]) in @endif">
                            <div class="card-block">
                                <form method="POST" action="{{ action("\Marcop93\Webisan\WebisanController@run",$command["name"]) }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="backAction" value="{{ $backAction }}">
                                    {{-- Arguments --}}
                                    @if(count($command["definition"]->getArguments()) > 0)
                                        @foreach($command["definition"]->getArguments() as $argument)
                                            <div class="form-group">
                                                <label for="argument_{{ $argument->getName() }}" class="text-capitalize">{{ $argument->getName() }}</label>
                                                <input type="text" class="form-control" id="argument_{{ $argument->getName() }}" name="argument_{{ $argument->getName() }}" placeholder="{{ is_array($argument->getDefault()) ? '' : $argument->getDefault() }}">
                                                <small class="form-text text-muted">{{ $argument->getDescription() }}</small>
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- Options --}}
                                    @if(count($command["definition"]->getOptions()) > 0)
                                        @foreach($command["definition"]->getOptions() as $option)
                                            <div class="form-check">
                                                {{-- In Case it needs text --}}
                                                @if($option->getDefault() !== false)
                                                    <label for="option_{{ $option->getName() }}" class="text-capitalize">{{ $option->getName() }}</label>
                                                    <input type="text" class="form-control" id="option_{{ $option->getName() }}" name="option_{{ $option->getName() }}" placeholder="{{ is_array($option->getDefault()) ? '' : $option->getDefault() }}">
                                                    <small class="form-text text-muted">{{ $option->getDescription() }}</small>
                                                @else
                                                    <label for="option_{{ $option->getName() }}" class="text-capitalize form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="option_{{ $option->getName() }}" name="option_{{ $option->getName() }}" placeholder="{{ is_array($option->getDefault()) ? '' : $option->getDefault() }}">
                                                        {{ $option->getName() }}
                                                        <small class="form-text text-muted">{{ $option->getDescription() }}</small>
                                                @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    <button class="btn btn-primary float-xs-right" type="submit" name="action">Execute</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            No commands received
        @endif
    </div>
@endsection