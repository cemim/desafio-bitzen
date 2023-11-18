<?php

namespace App\Http\Controllers;

use App\Models\PartnerCompany;

use Illuminate\Http\Request;

class ControllerPartnerCompany extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = PartnerCompany::all();
        return response()->json($partners, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $partner = $this->loadPartner($request);        
        $partner->save();
        return response()->json($partner, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $partner = PartnerCompany::find($id);
        return response()->json($partner, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $partner = $this->loadPartner($request, $id);
        $partner->save();
        return response()->json($partner, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $partner = PartnerCompany::find($id);
        if(isset($partner)){
            $partner->delete();
            return response()->json($partner, 200);
        }
        return response()->json('Parceiro não localizado', 200);;
    }

    private function loadPartner($request, $id = null) {
        if($id !== null) {
            $partner = PartnerCompany::find($id);
        } else {
            $partner = new PartnerCompany();
        }

        $partner->razao_social = $request->input("razao_social");
        $partner->nome_fantasia = $request->input("nome_fantasia");
        $partner->cnpj = $request->input("cnpj");
        $partner->dt_fundacao = $request->input("dt_fundacao");
        $partner->email_responsavel = $request->input("email_responsavel");
        $partner->nome_responsavel = $request->input("nome_responsavel");

        return $partner;
    }

}
