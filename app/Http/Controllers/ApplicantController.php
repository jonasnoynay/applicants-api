<?php

namespace App\Http\Controllers;

use Validator;
use App\Applicant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\ApplicantResource;
use Illuminate\Validation\ValidationException;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ApplicantResource::collection(Applicant::all());
    }

    /**
     * Store a new applicant instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get $request data
        $data = $request->only(['name', 'email', 'isHired']);

        //validate data
        $validator = Validator::make($data, [
            'name'  => 'required|max:45',
            'email'  => 'required|max:65',
            'isHired' => 'required|boolean'
        ]);

        //throw exception if validation fails
        if($validator->fails()) throw new ValidationException($validator);

        //check for the applicant if exists
        $applicant = Applicant::where('email', $data['email'])->first();

        if($applicant) {

            //prevent already hired applicants
            if($applicant->isHired) return abort(400, __('apiMsg.already_hired'));

            //if last application date is still within 6 months
            if($applicant->created_at > Carbon::now()->subMonths(6)) {
                abort(400, __('apiMsg.reapply'));
            } else {
                //update application status
                $data['created_at'] = now();
                $applicant->update($data);

                //return applicant
                return response()->json([
                    'success' => true,
                    'message' => __('apiMsg.applicant_updated'),
                    'data'    => new ApplicantResource($applicant)
                ]);
            }
        }

        //create a new applicant
        $applicant = Applicant::create($data);

       //return applicant
       return response()->json([
        'success' => true,
        'message' => __('apiMsg.applicant_created'),
        'data'    => new ApplicantResource($applicant)
    ]);
    }

    /**
     * Display the specified applicant.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get applicant or fail
        $applicant = Applicant::findOrFail($id);

        //return applicant
        return new ApplicantResource($applicant);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //validate data
        $validator = Validator::make($request->all(), [
            'name'  => 'max:45',
            'email'  => 'unique:applicants,email, '.$request->id. '|max:65',
            'isHired' => 'boolean'
        ]);

        //throw exception if validation fails
        if($validator->fails()) throw new ValidationException($validator);
        
        //get applicant or fail
        $applicant = Applicant::findOrFail($request->id);

        //assign data
        if($request->has('name')) $applicant->name = $request->name;
        if($request->has('email')) $applicant->email = $request->email;
        if($request->has('isHired')) $applicant->isHired = $request->isHired;
        $applicant->save();

        //return applicant
        return response()->json([
            'success' => true,
            'message' => __('apiMsg.applicant_updated'),
            'data'    => new ApplicantResource($applicant)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       //get applicant or fail
       $applicant = Applicant::findOrFail($request->id);

        //delete applicant
        $applicant->delete();

        //return json response
        return response()->json([
            'success' => true,
            'message' => __('apiMsg.applicant_deleted')
        ]);
    }
}
