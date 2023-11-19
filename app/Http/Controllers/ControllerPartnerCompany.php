<?php

namespace App\Http\Controllers;

use App\Models\PartnerCompany;
use App\Services\CNPJService;
use Illuminate\Http\Request;

class ControllerPartnerCompany extends Controller
{
    protected $cnpjService;

    public function __construct(CNPJService $cnpjService)
    {
        $this->cnpjService = $cnpjService;
    }

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
        $isValid = $this->validateCNPJ($partner->cnpj);
        
        if($isValid['status'] === 1){
            $partner->save();
            return response()->json($partner, 200);
        }

        return response()->json($isValid, 400);                
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $partner = PartnerCompany::withTrashed()->find($id);
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
        $isValid['status'] = 1;

        if($partner->cnpj_anterior !== $partner->cnpj){
            $isValid = $this->validateCNPJ($partner->cnpj);
        }
        
        if($isValid['status'] === 1){
            $partner->offsetUnset('cnpj_anterior');
            $partner->save();
            return response()->json($partner, 200);
        }

        return response()->json($isValid, 400);  
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
        return response()->json('Parceiro não localizado', 200);
    }

    public function restore(string $id) {
        $partner = PartnerCompany::withTrashed()->find($id);
        $partner->restore();

        return response()->json($partner, 200);;        
    }

    public function searchRazaoSocial($razaoSocial) {
        $partner = PartnerCompany::where('razao_social', 'like', '%'. $razaoSocial .'%')->get();
        return $partner;
    }

    private function loadPartner($request, $id = null) {
        if($id !== null) {
            $partner = PartnerCompany::find($id);
            $partner->cnpj_anterior = $partner->cnpj;
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

    // Return Array
    // Ex: ['status'=>1, 'msg'=>'CNPJ OK']
    // status 1 - OK
    // status 0 - Não é valido    
    private function validateCNPJ($cnpj):array {
        $dataCNPJ = $this->cnpjService->consultarCNPJ($cnpj);
        $partner = PartnerCompany::withTrashed()->where(['cnpj'=>$cnpj])->first();
        
        // Verifica se o CNPJ existe pela API
        if(isset($dataCNPJ["status"]) && $dataCNPJ["status"] === 0){
            return ['status'=>0, 'msg'=>'CNPJ Nao e valido'];
        }
        
        // Verificar se já existe o CNPJ
        if($partner) {
            if($partner->deleted_at){
                return ['status'=>0, 'msg'=>'CNPJ ja existe na base de dados, mas esta inativo. ID: ' . $partner->id];
            }
            return ['status'=>0, 'msg'=>'CNPJ ja existe na base de dados'];
        }

        // Inicio Verificar CNAE
        $bolCNAE = false;

        if($dataCNPJ['cnae_fiscal'] == '6202300'){
            $bolCNAE = true;
        }

        foreach($dataCNPJ['cnaes_secundarios'] as $cnae) {
            if($cnae['codigo'] == '6202300') {
                $bolCNAE = true;
            }
        }

        if($bolCNAE === false) {
            return ['status'=>0, 'msg'=>'CNAE invalido, deve ser igual a 6202300 - Desenvolvimento e licenciamento de programas de computador customizaveis'];
        }
        // Fim Verificar CNAE

        return ['status'=>1, 'msg'=>'CNPJ OK'];
    }

}
