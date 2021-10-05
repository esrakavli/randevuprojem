<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentNote;
use App\Models\WorkingHours;
use Illuminate\Http\Request;

class indexController extends Controller

{
    public function detailStore(Request $request)
    {
        $returnArray = [];
        $all = $request->except('_token');
        $create = AppointmentNote::create([
            'appointmentId'=>$all['id'],
            'text'=>$all['text']
        ]);
        if($create){
            $returnArray['status'] = true;

        }
        else{
            $returnArray['status'] = false;
        }

        return response()->json($returnArray);
    }


    public function detail($id){
     $returnArray =[];
     $data = Appointment::where('id',$id)->get();
     $data[0]['working'] =  WorkingHours::getString($data[0]['workingHour']);
     $data[0]['notification']=($data[0]['notification_type'] == NOTIFICATION_EMAIL) ? 'Email' : 'Sms';
     $returnArray['data'] = $data[0];
     $returnArray['comment'] =AppointmentNote::where('appointmentId',$id)->orderBy('id','desc')->get();
     return response()->json($returnArray);

    }
    public function process(Request $request)
    {
        $all = $request->except('_token');
        Appointment::where('id',$all['id'])->update(['isActive'=>$all['type']]);
        return response()->json(['status'=>true]);
    }


    public function all(){
        $returnArray =[];

        // waitinglist
        $returnArray['waiting']=Appointment::where('isActive',0)->orderBy('workingHour','asc')->paginate(3,['*'],'waiting_page');
        $returnArray['waiting']->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });


        // cancellist
        $returnArray['cancel']=Appointment::where('isActive',2)->orderBy('workingHour','asc')->paginate(3,['*'],'cancel_page');
        $returnArray['cancel']->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });


        // list
        $returnArray['list']=Appointment::where('isActive',1)->where('date','>',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(3,['*'],'list_page');
        $returnArray['list']->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });


        // lastlist
        $returnArray['last_list']=Appointment::where('isActive',1)->where('date','<',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(3,['*'],'last_list_page');
        $returnArray['last_list']->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });



        //todaylist
        $returnArray['today_list']=Appointment::where('date',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(3,['*'],'today_list_page');
        $returnArray['today_list']->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });

        return response()->json($returnArray);

    }


    public function getWaitingList(){
        $data=Appointment::where('isActive',0)->orderBy('workingHour','asc')->paginate(9);
        $data->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });
        return response()->json($data);
    }
    public function getCancelList(){
        $data=Appointment::where('isActive',2)->orderBy('workingHour','asc')->paginate(9);
        $data->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });
        return response()->json($data);
    }

    public function getList(){
        $data=Appointment::where('isActive',1)->where('date','>',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(9);
        $data->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });
        return response()->json($data);
    }
    public function getLastList(){
        $data=Appointment::where('isActive',1)->where('date','<',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(9);
        $data->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });
        return response()->json($data);
    }
    public function getTodayList(){
        $data=Appointment::where('date',date("Y-m-d"))->orderBy('workingHour','asc')->paginate(9);
        $data->getCollection()->transform(function ($value){
            $value['working']=WorkingHours::getString($value['workingHour']);
            return $value;
        });
        return response()->json($data);
    }
}
