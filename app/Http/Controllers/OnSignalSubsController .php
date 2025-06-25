<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OnsignalSub;

class OnSignalSubsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function send_notification(Request $request)
    {
        try {
            $contents = $request->message;
            $subcriptionIds = OnsignalSub::pluck('subscription_id')->toArray();
            $url = $request->url;
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('ONESIGNAL_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'included_segments' => ['All'],
                'included_player_ids' => $subcriptionIds,
                'contents' => ['en' => $contents],
                'url' => $url,
            ]);

            //return response()->json($response->json());
            return redirect()->back()->with('success_message', 'Notification send successfully.');
        } catch (\Exception $e) {
            //return response()->json(['error' => $e->getMessage()], 500);
            return redirect()->back()->with('error_message', 'Unable to send notification , please try again.');
        }
    }

    public function onsignalsub_subscriber_store(Request $request)
    {
        $check = $this->model()::where('subscription_id', $request->subscription_id)->first();
        if ($check) {
            $save = $this->savedata($type = 'update', $request, $id = $check->id);
        }else{
            $save = $this->savedata($type = 'save', $request, $id = 'null');
        }



        if ($save) {
            return response()->json(['success' => 'Data added successfully.'], 200);
        } else {
            return response()->json(['error' => 'Unable to add data.'], 500);
        }
    }

    public function model()
    {
        $model = new OnsignalSub();
        return $model;
    }

    public function index()
    {
        $alldata = $this->model()::paginate(20);
        return view('admin.onsignalsub.list', compact('alldata'));
    }


    public function create()
    {
        return view('admin.onsignalsub.add');
    }

    public function savedata($type,$request,$id)
    {
        $input = $request->all();
        if($type == 'save'){
            $data = $this->model();
            $save = $data->create($input);
        }
        else{
            $data = $this->model()::find($id);
            $save = $data->update($input);
        }
        return $save;
    }

    public function store(Request $request)
    {

        $check = $this->model()::where('subscription_id',$request->subscription_id)->first();
        if($check){
            return redirect()->back()
                         ->with('error_message', 'Email already exists.');
        }
        $save = $this->savedata($type='save',$request,$id='null');

        if($save){
            return redirect()->back()
                         ->with('success_message', 'Data added successfully.');
        }
        else{
            return redirect()->back()
                         ->with('error_message', 'Unable to add data.');
        }
    }


    public function edit($id)
    {
        $singledata = $this->model()::find($id);
        return view('admin.onsignalsub.edit', compact('singledata'));
    }


    public function update(Request $request,$id)
    {
        $save = $this->savedata($type='update',$request,$id);

        if($save){
            return redirect()->route('admin.onsignalsub.list')
                         ->with('success_message', 'Data updated successfully.');
        }
        else{
            return redirect()->back()
                         ->with('error_message', 'Unable to update data.');
        }
    }


    public function destroy($id)
    {
        $data = $this->model()::find($id);
        $delete = $data->delete();
        if($delete){
            return redirect()->back()
                         ->with('success_message', 'Data deleted successfully.');
        }
        else{
            return redirect()->back()
                         ->with('error_message', 'Unable to delete data.');
        }
    }

}
