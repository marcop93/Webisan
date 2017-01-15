<?php

namespace Marcop93\Webisan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Exception;

class WebisanController extends Controller {
    public function show($option = null, $search = null) {
        $commandsMenu = $this->getCommandsArray();

        if (is_null($option))
            return redirect()->action('\Marcop93\Webisan\WebisanController@show',array_values($commandsMenu)[0]["link"]);

        $currentOption = $option;
        $option .= ":*";
        $backAction = action('\Marcop93\Webisan\WebisanController@show',$currentOption);

        if (!array_key_exists($option, $commandsMenu))
            abort(404);

        return view("Webisan::commands",compact("commandsMenu","option","currentOption","search","backAction"));
    }
    public function run(Request $request, $command) {
        $input = "~$ php artisan " . $command;

        if (array_key_exists('argument_name', $request->all()))
            $this->validate($request, ['argument_name' => 'required']);

        if (array_key_exists('argument_id', $request->all()))
            $this->validate($request, ['argument_id' => 'required']);

        $inputs = $request->except('_token', 'command');

        $params = [];
        foreach ($inputs as $key => $value) {
            if ($value != '' && $key!= "backAction") {
                $name = starts_with($key, 'argument') ? substr($key, 9) : '--' . substr($key, 7);
                $params[$name] = $value;
                $input .= " " . $name . "=" . $value;
            }
        }

        try {
            Artisan::call($command, $params);
            $output = Artisan::output();
            if ($output=="")
                $output = "No Output\n";
            $type = "success";
        } catch (Exception $e) {
            $output = $e->getMessage();
            $type = "danger";
        }

        if ($request->has("backAction"))
            $action = $request->input("backAction");
        else
            $action = back();

        return redirect()->to($action)->with(['output'=> $output,'command'=> $command, 'input'=> $input,'type'=>$type]);
    }
    public function settings() {
        $currentOption = "settings";
        $commandsMenu = $this->getCommandsArray();

        $commands = $this->getCommandsArray(false);
        $settings = config('webisan');
        $commandsSelect = [];
        $commandsSelected = [];

        foreach ($commands as $commandGroup) {
            foreach ($commandGroup["subcommands"] as $command) {
                $commandsSelect[$commandGroup["title"]][$command["name"]] = $command["name"];
            }
        }
        if (is_array($settings["ignore"]) || is_object($settings["ignore"])) {
            foreach ($settings["ignore"] as $commandName => $commandValue) {
                $commandsSelected[$commandName] = $commandName;
            }
        }

        return view("Webisan::settings",compact("commandsMenu","commands","currentOption","settings","commandsSelect","commandsSelected"));
    }
    public function settingsSave(Request $request) {
        $settings = config('webisan');
        $newSettings = $settings;
        $newSettings["ignore"] = [];
        if (is_array($request->input("ignore")) || is_object($request->input("ignore"))) {
            foreach ($request->input("ignore") as $ignoreCommand) {
                $newSettings["ignore"][$ignoreCommand] = true;
            }
        }

        $newSettings["customRoutes"] = ($request->input("customRoutes")) ? true : false;

        $output = "<?php\n\nreturn " . var_export($newSettings, true) . ";\n";
        if (File::put(config_path() . "/webisan.php", $output) === false)
            Session::flash("settings","failed");
        else
            Session::flash("settings","success");

        return redirect()->back();
    }
    public function search(Request $request) {
        $commandsToReturn = array();
        $query = $request->input("q");
        if (!empty($query)) {
            $commandList = $this->getCommandsArray();
            foreach ($commandList as $commandGroup) {
                foreach ($commandGroup["subcommands"] as $command) {
                    if (strpos($command["name"], $query) !== false) {
                        $commandsToReturn[] = array(
                            "id" => $command["name"],
                            "name" => $command["name"],
                            "link" => action('\Marcop93\Webisan\WebisanController@show',["command"=>$commandGroup["link"],"search"=>$command["name"]])
                        );
                    }
                }
            }
        }

        return response()->json($commandsToReturn, 200);
    }
    private function getCommandsArray($filtered = true) {
        $ignoreList = config('webisan.ignore');
        $commandsRaw = [];
        $commandsList = ["other:*"=> array(
            "name"          => "other:*",
            "link"          => "other",
            "title"         => "+ <span class='hidden-sm-up'>Other</span>",
            "subcommands"   => array()
        )];
        foreach (Artisan::all() as $name=>$command) {
            if (!isset($ignoreList[$name]) || !$filtered) {
                $commandsRaw[$name] = array(
                    "name" => $command->getName(),
                    "selector" => str_replace(":", "_", $command->getName()),
                    "aliases" => $command->getAliases(),
                    "definition" => $command->getDefinition(),
                    "help" => $command->getHelp(),
                    "description" => $command->getDescription(),
                    "synopsis" => $command->getSynopsis(),
                    "usages" => $command->getUsages(),
                );
            }
        }

        foreach ($commandsRaw as $command) {
            if (str_contains($command["name"], ":")) {
                $prefix = strtok($command["name"], ':');

                if (!array_key_exists($prefix . ":*",$commandsList)) {
                    $commandsList[$prefix . ":*"] = array(
                        "name"          => $prefix . ":*",
                        "link"          => $prefix,
                        "title"         => $prefix,
                        "subcommands"   => array($command["name"] => $command)
                    );
                } else {
                    $commandsList[$prefix . ":*"]["subcommands"][$command["name"]] = $command;
                }
            } else if (!str_contains($command["name"], ":*"))
                $commandsList["other:*"]["subcommands"][] = $command;
            unset($commandsList[$command["name"]]);
        }

        foreach ($commandsList as $key=>$command) {
            if (count($command["subcommands"])==1) {
                reset($command["subcommands"]);
                $first_key = key($command["subcommands"]);
                $commandsList["other:*"]["subcommands"][$command["subcommands"][$first_key]["name"]] = $command["subcommands"][$first_key];
                unset($commandsList[$key]);
            }
        }

        $temp = $commandsList["other:*"];
        unset($commandsList["other:*"]);
        $commandsList["other:*"] = $temp;

        return $commandsList;
    }
}