<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Email;
use App\Models\Phone;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Contacts API",
 *      description="API for contacts",
 *      @OA\Contact(
 *          email="alvaroalejo25@gmail.com",
 *          name="Alvaro Zarabanda"
 *      )
 * )
 */
class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contacts",
     *     tags={"Contactos"},
     *     summary="Obtener una lista de contactos",
     *     @OA\Response(response=200, description="Operación exitosa"),
     *     @OA\Tag(name="Contacts"),
     * )
     */
    public function index()
    {
        $contacts = Contact::with('phones', 'emails')->get();

        return response()->json($contacts);
    }



    /**
     * @OA\Post(
     *     path="/api/contacts",
     *     tags={"Contactos"},
     *     summary="Crear un nuevo contacto",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nombre del contacto"),
     *             @OA\Property(property="document", type="string", example="123456789"),
     *             @OA\Property(property="address", type="string", example="Dirección del contacto")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contacto creado exitosamente"),
     *     @OA\Tag(name="Contacts"),
     * )
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
     * @OA\Get(
     *     path="/api/contacts/{contact}",
     *     tags={"Contactos"},
     *     summary="Obtener detalles de un contacto específico",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Response(response=200, description="Detalles del contacto"),
     *     @OA\Response(response=404, description="Contacto no encontrado"),
     *     @OA\Tag(name="Contacts"),
     * )
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
     * @OA\Put(
     *     path="/api/contacts/{contact}",
     *     tags={"Contactos"},
     *     summary="Actualizar un contacto existente",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nuevo nombre del contacto"),
     *             @OA\Property(property="document", type="string", example="987654321"),
     *             @OA\Property(property="address", type="string", example="Nueva dirección del contacto")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contacto actualizado exitosamente"),
     * )
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
     * @OA\Delete(
     *     path="/api/contacts/{contact}",
     *     tags={"Contactos"},
     *     summary="Eliminar un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Response(response=200, description="Contacto eliminado exitosamente"),
     * )
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

    /**
     * @OA\Post(
     *     path="/api/contacts/{contact}/emails",
     *     tags={"Emails"},
     *     summary="Agregar un email a un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="correo@dominio.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Email agregado exitosamente"),
     *     @OA\Response(response=404, description="Contacto no encontrado"),
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/contacts/{contact}/emails/{email}",
     *     tags={"Emails"},
     *     summary="Eliminar un email de un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="ID del email",
     *     ),
     *     @OA\Response(response=200, description="Email eliminado exitosamente"),
     *     @OA\Response(response=404, description="Contacto o email no encontrado"),
     * )
     */
    public function deleteEmail(Contact $contact,Email $email)
    {
        $email = $contact->emails()->find($email->id);

        $email->delete();

        return response()->json([
            'message' => 'Email deleted successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/contacts/{contact}/emails/{email}",
     *     tags={"Emails"},
     *     summary="Actualizar un email de un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="ID del email",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="nuevo_correo@dominio.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Email actualizado exitosamente"),
     *     @OA\Response(response=404, description="Contacto o email no encontrado"),
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/contacts/{contact}/phones",
     *     tags={"Telefonos"},
     *     summary="Agregar un teléfono a un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="123456789")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Teléfono agregado exitosamente"),
     *     @OA\Response(response=404, description="Contacto no encontrado"),
     * )
     */
    public function addPhone(Request $request, Contact $contact){
        $contact->phones()->create([
            'phone' => $request->phone,
        ]);

        return response()->json([
           'message' => 'Phone added successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/contacts/{contact}/phones/{phone}",
     *     tags={"Telefonos"},
     *     summary="Eliminar un teléfono de un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="path",
     *         required=true,
     *         description="ID del teléfono",
     *     ),
     *     @OA\Response(response=200, description="Teléfono eliminado exitosamente"),
     *     @OA\Response(response=404, description="Contacto o teléfono no encontrado"),
     * )
     */
    public function deletePhone(Contact $contact,Phone $phone){
        $phone = $contact->phones()->find($phone->id);

        $phone->delete();

        return response()->json([
          'message' => 'Phone deleted successfully',
            'contact' => $contact->load('phones', 'emails')
        ]);
    }


    /**
     * @OA\Put(
     *     path="/api/contacts/{contact}/phones/{phone}",
     *     tags={"Telefonos"},
     *     summary="Actualizar un teléfono de un contacto",
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID del contacto",
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="path",
     *         required=true,
     *         description="ID del teléfono",
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="987654321")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Teléfono actualizado exitosamente"),
     *     @OA\Response(response=404, description="Contacto o teléfono no encontrado"),
     * )
     */
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
