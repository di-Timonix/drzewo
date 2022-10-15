<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Woods;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

class QueryDataWood extends Controller
{

    private array $out = [];

    public function getData(Request $request)
    {
        if ($request["parent"] === "#") {
            $data = DB::table("wood")->where("parent_id", "=", "0")->get();
        } else {
            $data = DB::table("wood")->where("parent_id", "=", $request["parent"])->get();
        }

        $data = $this->parseData($data);

        return response($data, 200, ["Content-Type" => 'application/json; charset=utf-8']);;
    }

    private function parseData($data)
    {
        $buffer = [];

        foreach ($data as $key => $value) {
            if ($value->{"parent_id"} === 0) {
                $type = "root";
            }

            $buffer[] = [
                "id" => (string) ($value->{"id_item"}),
                "text" => $value->{"name"},
                "icon" => "fa icon-lg kt-font-info " . (strpos($value->{"name"}, ".") ? "fa-file" : "fa-folder"),
                "children" => true,
                "type" => $type ?? (strpos($value->{"name"}, ".") ? "file" : "folder")
            ];
        }

        return json_encode($buffer);
    }

    private function rename($resurces)
    {
        $buffer = [];

        foreach ($resurces as $key => $value) {
            $id = $value["id"];
            $text = $value["text"];
            $parent = $value["parent"];

            if (!is_string($id) || !is_string($text) || !is_string($parent)) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Nazwa musi być Tekstem!"];
                break;
            }

            if (strlen($id) === 0 || strlen($text) === 0 || strlen($parent) === 0) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Nazwa musi być Tekstem!"];
                break;
            }
            
            if ($parent === "#") {
                $parent = 0;
            }

            if (count(DB::select("SELECT *  FROM `wood` WHERE `name` LIKE '$text' AND `parent_id` LIKE '$parent'")) !== 0) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Taka nazwa już istnieje!"];
                break;
            }


            DB::update("UPDATE `wood` SET `name`='$text' WHERE `id_item` LIKE '$id'");

            array_push($buffer, ["id_item" => $id, "name" => $text]);
            $this->out["ok"]["rename"][] = ["poz" => $key, "id" => $id, "text" => $text];
        }

        if (isset($this->out["error"])) {
            return response()->json(['status' => "info", 'msg' => "Taka nazwa Już istnieje", "data" => $this->out], 200, ["Content-Type" => 'application/json; charset=utf-8']);
        }
    }
    private function dell($resurces)
    {
        $buffer = [];

        foreach ($resurces as $key => $value) {
            $id = $value["id"];

            if (!is_string($id)) {
                $this->out["error"][] = ["id" => $id, "text" => "brak nazwy", "mess" => "Nazwa musi być Tekstem!"];
                break;
            }
            if (strlen($id) === 0) {
                $this->out["error"][] = ["id" => $id, "text" => "brak nazwy", "mess" => "Nazwa musi być Tekstem!"];
                break;
            }
            
            // dell childrent
            $this->dellall($id);
            DB::delete("DELETE FROM `wood` WHERE `id_item` LIKE '$id'");

            array_push($buffer, ["id" => NULL, "id_item" => $id]);
            $this->out["ok"]["dell"][] = ["poz" => $key, "id" => $id, "mess" => "Usunięto element!"];
        }
    }
    private function add($resurces)
    {
        $dbs = DB::table("wood");

        $buffer = [];

        $data = DB::table("wood")->pluck('id_item')->toArray();

        foreach ($resurces as $key => $value) {
            $id = $value["id"];
            $pos = $value["position"];
            $text = $value["text"];
            $parent = $value["parent"];

            if (!is_string($id) || !is_string($pos) || !is_string($text) || !is_string($parent)) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Nazwa musi być Tekstem!"];
                break;
            }
            if (strlen($id) === 0 || strlen($pos) === 0 || strlen($text) === 0 || strlen($parent) === 0) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Nazwa musi być Tekstem!"];
                break;
            }

            if (array_search($id, $data)) {
                $id = $id . uniqid();
            }


            if ($parent === "#") {
                $parent = 0;
            }

            if (count(DB::select("SELECT *  FROM `wood` WHERE `name` LIKE '$text' AND `parent_id` LIKE '$parent'")) !== 0) {
                $this->out["error"][] = ["id" => $id, "text" => $text, "mess" => "Taka nazwa już istnieje!"];
                break;
            }

            if ($pos === "#") {
                $pos = 0;
            }

            array_push($buffer, ["id" => NULL, "id_item" => $id, "name" => $text, "parent_id" => $pos]);
            $this->out["ok"]["add"][] = ["poz" => $key, "id" => $id, "text" => $text, "mess" => "Dodano Element!"];
        }

        $dbs->insert($buffer);
    }

    private function saveData($resurces)
    {
        $buffer = [];

        foreach ($resurces as $key => $value) {
            $id = $value["id"];
            $text = $value["text"];
            $parent = $value["parent"];

            if ($parent === "#") {
                $parent = 0;
            }

            DB::update("UPDATE `wood` SET `parent_id`='$parent' WHERE `id_item` LIKE '$id'");

            array_push($buffer, ["id_item" => $id, "name" => $text]);
            $this->out["ok"]["fine"][] = ["poz" => $key, "id" => $id, "text" => $text];
        }
    }

    private function dellall(string $id)
    {
        $childrents = DB::select("SELECT * FROM `wood` WHERE `parent_id` = '$id'");

        foreach ($childrents as $key => $value) {
            $id = $value->{"id_item"};

            DB::delete("DELETE FROM `wood` WHERE `id_item` LIKE '$id'");

            if (count(DB::select("SELECT * FROM `wood` WHERE `parent_id` = '$id'")) !== 0) {
                $this->dellall($id);
            }
        }

        DB::delete("DELETE FROM `wood` WHERE `id_item` LIKE '$id'");
    }

    public function updateData(Request $request)
    {

        // validate data
        $valid = $request->validate([
            "updateNode.*.id" => "string|min:1|max:100",
            "updateNode.*.text" => "string|min:1|max:1000",
            "updateNode.*.parent" => "string|min:1|max:100",

            "addNode.*.id" => "string|min:1|max:100",
            "addNode.*.position" => "string|min:1|max:100",
            "addNode.*.text" => "string|min:1|max:1000",
            "addNode.*.parent" => "string|min:1|max:100",

            "dellNode.*.id" => "string|min:1|max:100",

            "updateNode.*" => "array",
            "addNode.*" => "array",
            "dellNode.*" => "array",
            "saveData.*" => "array",
        ]);

        $addNode = isset($valid['addNode']) ? $valid['addNode'] : [];
        $updateNode = isset($valid['updateNode']) ? $valid['updateNode'] : [];
        $dellNode = isset($valid['dellNode']) ? $valid['dellNode'] : [];
        $saveData = isset($valid['saveData']) ? $valid['saveData'] : [];


        $isNull = true;

        if (count($addNode) !== 0) {
            $isNull = false;

            $this->add($addNode);
        }

        if (count($updateNode) !== 0) {
            $isNull = false;

            $this->rename($updateNode);
        }

        if (count($dellNode) !== 0) {
            $isNull = false;

            $this->dell($dellNode);
        }

        if (count($saveData) !== 0) {
            $isNull = false;

            $this->saveData($saveData);
        }

        if ($isNull === true) {
            return response()->json(['status' => "warning", 'msg' => "Brak danych do aktulizacji"], 200, ["Content-Type" => 'application/json; charset=utf-8']);
        }

        if (isset($this->out["error"])) {
            return response()->json(['status' => "error", 'msg' => "Poniższe elementy wymagają poprawy: ", "data" => $this->out], 200, ["Content-Type" => 'application/json; charset=utf-8']);
        }

        // albo dodać metoda formatującą odpowiedź....
        return response()->json(['status' => "success", 'msg' => "Wykonano Operację poprawnie", "data" => $this->out], 200, ["Content-Type" => 'application/json; charset=utf-8']);
    }
}
