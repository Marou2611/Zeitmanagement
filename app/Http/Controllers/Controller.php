<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected $className = 'Controller';
    protected $entityName = 'controllers';
    protected $fields = [];
    protected $validations = [];
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getIndex()
    {
        $class = $this->className;
        $entities = $class::paginate(20);
        return view($this->entityName . '.index')->with('entities', $entities);
    }

    public function getAdd()
    {
        return view($this->entityName . '.add');
    }

    public function postSave(Request $req)
    {
        $req->validate($this->validation);
        $class = $this->className;
        $entity = new $class();
        foreach ($this->fields as $field) {
            $entity->$field = $req->input($field);
        }
        $entity->save();

        return redirect($this->entityName . '/index')->with('success');
    }

    public function getEdit($id)
    {
        $class = $this->className;
        $entity = $class::find($id);
        if ($entity) {
            return view($this->entityName . '.edit')->with('entity', $entity);
        }
        return redirect($this->entityName . '/index');
    }

    public function postUpdate(Request $req, $id)
    {
        $req->validate($this->validations);
        $class = $this->className;
        $entity = $class::find($id);
        if ($entity) {
            foreach ($this->fields as $field) {
                $entity->$field = $req->input($field);
            }
            $entity->save();
        }
        return redirect($this->entityName . '/index');
    }

    public function getShow($id)
    {
        $class = $this->className;
        $entity = $class::find($id);
        if ($entity) {
            return view($this->entityName . '.show')->with('entity', $entity);
        }
        return redirect($this->entityName . '/index');
    }

    public function getDelete($id)
    {
        $class = $this->className;
        $entity = $class::find($id);

        if ($entity) {
            try {
                $entity->delete();
            } catch (\Exception $e) {
                return redirect($this->entityName . '/index')->with('error', 'Objekt kann nicht gelöscht werden. ');
            }
        }
        return redirect($this->entityName . '/index');
    }

    public function getJson()
    {
        $class = $this->className;
        return response()->json($class::all());
    }
}
