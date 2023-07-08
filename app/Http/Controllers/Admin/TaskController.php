<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Task;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = User::find(Auth::user()->id)->tasks()->orderBy('status_order', 'asc')->get();

        return view('admin.tasks.index', compact('tasks'));
    }

    public function store(TaskRequest $request)
    {
        $maxnummerDB = User::find(Auth::user()->id)->tasks()->select('status_order', DB::raw('MIN(status_order) as status_order'))->where('status', $request->status)->first();
        $maxnummer = $maxnummerDB->status_order;
        $status_order = $maxnummer -1;
        $request->user()->tasks()->create([
            'task' => $request->task,
            'status' => $request->status,
            'color' => '#'.$request->color,
            'description' => $request->description,
            'status_order' => $status_order
        ]);

        return response()->json([], 200);
    }

    public function update(Task $task, Request $request)
    {
        $task = Task::findOrFail($task->id);
        $this->authorize('update', $task);
        $task->status = $request['status'];
        $task->save();
        return response()->json([], 200);
    }

    public function updatePositions(Request $request)
    {
        if ($request['positions']) {
            foreach ($request['positions'] as $position => $item) {
                $itemok = preg_replace("/[^0-9]/", "", $item);
                $positionok = $position +1;
                $task = Task::find($itemok);

                $task->status_order = $positionok;
                $task->save();
            }
        }

            return response()->json([], 200);
    }

    public function destroy(Task $task, Request $request)
    {
        $task = Task::findOrFail($task->id);
        $this->authorize('destroy', $task);
        $task->delete();
        return response()->json([], 200);
    }

    public function updateModal(Request $request)
    {
        $task = Task::findOrFail($request['taskid']);
        $this->authorize('update', $task);


        $old_description = $task->description ? true : false;

            $task->description = $request['task-description'];
            $task->color = '#'.$request['task-color'];
            $task->task = $request['task-name'];

            $task->save();
            return response()->json([
                'new_description' =>  $task->description,
                'new_color' => $task->color,
                'old_description' => $old_description,
                'new_name' => $task->task
            ], 200);
    }
}
