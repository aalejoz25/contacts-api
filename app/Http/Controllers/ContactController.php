<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Email;
use App\Models\Phone;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::with('phones', 'emails')->get();

        return response()->json($contacts);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @OA/post("/contacts")
     * @tags({"Contacts"})
     * @summary("Create a new contact")
     * @description("Creates a new contact with the given name, document, and address")
     * @parameters({
     *     @parameter(name="name", in="formData", required=true, type="string"),
     *     @parameter(name="document", in="formData", required=true, type="string"),
     *     @parameter(name="address", in="formData", required=true, type="string"),
     * })
     * @responses({
     *     @response(code=201, description="Contact created successfully"),
     *     @response(code=422, description="Validation errors"),
     * })
     */
    public function store(Request $request)
    {
        //

        $errors = $request->validate([
            'name' => 'required',
            'document' => 'required',
            'address' => 'required',
        ]);

        if (!is_array($errors)) {
            return response()->json($errors, 422);
        }

        $contact = Contact::create([
            'name' => $request->name,
            'document' => $request->document,
            'address' => $request->address,
        ]);



        $data = [
          'message'=> "Contact Created Succesfully",
          'contact'=>$contact
        ];
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
        $contact->load('phones', 'emails');

        $data = [
            'message'=> 'Contact details',
            'contact'=>$contact
        ];
        return response()->json($data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Contact $contact, Request $request)
    {
        $errors = $request->validate([
            'name' => 'required',
            'document' => 'required',
            'address' => 'required',
        ]);

        if (!is_array($errors)) {
            return response()->json($errors, 422);
        }

        $contact->name = $request->name;
        $contact->document = $request->document;
        $contact->address = $request->address;

        $contact->update();

        $data = [
            'message'=> 'Contact Updated succesfully',
            'contact'=>$contact
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        $data = [
            'message'=> 'Contact Deleted succesfully',
            'contact'=>$contact
        ];
        return response()->json($data);
    }

    public function addEmail(Request $request, Contact $contact)
    {
        $contact->emails()->create([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Email added successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }

    public function deleteEmail(Contact $contact,Email $email)
    {
        $email = $contact->emails()->find($email->id);

        $email->delete();

        return response()->json([
            'message' => 'Email deleted successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }

    public function updateEmail(Contact $contact,int $email,Request $request)
    {

        $email = $contact->emails()->find($email);

        if ($email==null) {
            return response()->json([
                'message' => 'Email not found',
            ], 404);
        }

        $email->email = $request->email;
        $email->save();

        return response()->json([
            'message' => 'Email updated successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);

    }
    public function addPhone(Request $request, Contact $contact){
        $contact->phones()->create([
            'phone' => $request->phone,
        ]);

        return response()->json([
           'message' => 'Phone added successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }
    public function deletePhone(Contact $contact,Phone $phone){
        $phone = $contact->phones()->find($phone->id);

        $phone->delete();

        return response()->json([
          'message' => 'Phone deleted successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }

    public function updatePhone(Contact $contact,int $phone,Request $request)  {
        $phone = $contact->phones()->find($phone);

        if ($phone==null) {
            return response()->json([
               'message' => 'Phone not found',
            ], 404);
        }

        $phone->phone = $request->phone;
        $phone->save();

        return response()->json([
          'message' => 'Phone updated successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }




}
