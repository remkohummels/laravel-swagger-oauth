<?php

namespace App\Http\Controllers;

use App\Database\Eloquent\CompanyQueryBuilder;
use App\Http\Requests\StoreApplicationCompanyDataObject;
use App\Http\Requests\StoreCompany;
use App\Http\Requests\StoreCompanyData;
use App\Http\Requests\StoreStandardCompanyDataObject;
use App\Http\Requests\UpdateApplicationCompanyDataObject;
use App\Http\Requests\UpdateCompany;
use App\Http\Requests\UpdateStandardCompanyDataObject;
use App\Http\Resources\Company as CompanyResource;
use App\Http\Resources\CompanyCollection;
use App\Http\Resources\CompanyFull as CompanyFullResource;
use App\Models\Company;
use App\Models\CompanyData;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Composer\DependencyResolver\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Http\FormRequest;
use Laratrust\Laratrust;
use Lcobucci\JWT\Parser;

/**
 * Class CompanyController.
 */
class CompanyController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/companies",
     *     summary="The list of companies",
     *     tags={"company"},
     *     description="Get the list of companies",
     *     operationId="listCompanies",
     *     @OA\Response(
     *         response=200, description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ExistingCompany")
     *         ),
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/company_includes"),
     *     @OA\Parameter(ref="#/components/parameters/company_sorts"),
     *     @OA\Parameter(ref="#/components/parameters/company_fields"),
     *     @OA\Parameter(ref="#/components/parameters/company_filters"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_COMPANIES}}}
     * )
     *
     * @param CompanyQueryBuilder $queryBuilder
     *
     * @return CompanyCollection
     */
    public function index(CompanyQueryBuilder $queryBuilder)
    {
        return new CompanyCollection($queryBuilder->paginate());
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }


    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/companies",
     *     tags={"company"},
     *     summary="Add a new company to the application",
     *     operationId="storeCompany",
     *     @OA\Response(
     *         response=201, description="Company created",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/Company"),
     *     security={{"passport": {L5_SAGGER_MANAGE_COMPANIES}}}
     * )
     *
     * @param StoreUser $request
     * @param StoreStandardUserDataObject $standardRequest
     * @param StoreApplicationUserDataObject $standardRequest
     * @param Laratrust $laratrust
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompany $request, StoreStandardCompanyDataObject $standardRequest,
                          StoreApplicationCompanyDataObject $applicationRequest, Laratrust $laratrust)
    {
        $this->hasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $laratrust);

        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');

        $company = $this->saveCompany($request->validated(), $standardRequest->validated(), $applicationRequest->validated(), (int)$clientId);

        event(new Registered($company));

        return response(new CompanyFullResource($company), 201)
            ->header('Location', route('companies.show', $company->id));
    }


    /**
     * @param array $basicData
     * @param array $standardData
     * @param array $appData
     * @param int $clientId
     * @return Company
     */
    protected function saveCompany(array $basicData, array $standardData, array $appData, int $clientId, Company $company = null): Company
    {
        if (empty($company)) {
            $company = new Company($basicData[Company::BASIC_GROUP]);
        } else if(empty($basicData[Company::BASIC_GROUP]) === false) {
            $company->update($basicData[Company::BASIC_GROUP]);
        }

        $standardDataObjects = [];
        foreach ($standardData as $standardName => $standardMeta) {
            if (false === is_array($standardMeta)) {
                $standardMeta = ['value' => $standardMeta];
            }
            $standardMeta[CompanyData::NAME] = $standardName;
            $standardMeta[CompanyData::COMPANY_ID] = $company->id;

            $standardCompanyDataObject = $company->defaultData()
                ->where(CompanyData::NAME, '=', $standardMeta[CompanyData::NAME])
                ->whereNull(CompanyData::CLIENT_REFERENCE)
                ->firstOrNew($standardMeta);

            $this->saveCompanyData($standardCompanyDataObject, $standardMeta);

            $standardDataObjects []= $standardCompanyDataObject;
        }

        $appDataObjects = [];
        foreach ($appData as $appName => $appMeta) {
            if (false === is_array($appMeta)) {
                $appMeta = ['value' => $appMeta];
            }
            $appMeta[CompanyData::NAME] = $appName;
            $appMeta[CompanyData::COMPANY_ID] = $company->id;
            $appMeta[CompanyData::CLIENT_REFERENCE] = $clientId;

            $appCompanyDataObject = $company->applicationData()
                ->where(CompanyData::NAME, '=', $appMeta[CompanyData::NAME])
                ->where(CompanyData::CLIENT_REFERENCE, '=', $clientId)
                ->firstOrNew($appMeta);

            $this->saveCompanyData($appCompanyDataObject, $appMeta);

            $appDataObjects []= $appCompanyDataObject;
        }

        $company->save();

        $company->defaultData()->saveMany($standardDataObjects);
        $company->applicationData()->saveMany($appDataObjects);

        return $company;
    }


    /**
     * @param CompanyData $companyDataObject
     * @param array $noSqlData
     */
    protected function saveCompanyData(CompanyData $companyDataObject, array $noSqlData): void
    {
        $newDataKeys = array_keys($noSqlData);
        $oldData = collect($companyDataObject)->except($newDataKeys)->except(CompanyData::REQUIRED_FIELDS);
        if ($oldData->isNotEmpty()) {
            $companyDataObject->unset($oldData->keys()->toArray());
        }

        $companyDataObject->fillable($newDataKeys)->update($noSqlData);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/companies/{id}",
     *     summary="Find company by id",
     *     tags={"company"},
     *     description="Returns a singe company",
     *     operationId="showCompany",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingCompany")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_COMPANIES}}}
     * )
     *
     * @param Company $company
     *
     * @return CompanyResource
     */
    public function show(Company $company)
    {
        return new CompanyFullResource($company);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Company $company
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/companies/{id}",
     *     tags={"company"},
     *     summary="Updatge an existing company",
     *     operationId="updateCompany",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="company updated",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingCompany")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/Company"),
     *     security={{"passport": {L5_SAGGER_MANAGE_COMPANIES}}}
     * )
     *
     * @param UpdateCompany $request
     * @param UpdateStandardCompanyDataObject $standardRequest
     * @param UpdateApplicationCompanyDataObject $applicationRequest
     * @param Company       $company
     * @param Laratrust $laratrust
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompany $request, UpdateStandardCompanyDataObject $standardRequest,
                           UpdateApplicationCompanyDataObject $applicationRequest, Company $company, Laratrust $laratrust)
    {
        $this->ownsOrHasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $company, $laratrust);

        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');

        $this->saveCompany($request->validated(), $standardRequest->validated(), $applicationRequest->validated(), (int)$clientId, $company);

        return response(new CompanyFullResource($company));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/companies/{id}",
     *     summary="Deletes a company",
     *     tags={"company"},
     *     description="Deletes a singe company",
     *     operationId="deleteCompany",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(response=204, ref="#/components/responses/204"),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_MANAGE_COMPANIES}}}
     * )
     *
     * @param \App\Models\Company $company
     * @param Laratrust $laratrust
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Company $company, Laratrust $laratrust)
    {
        $this->hasRoleAndOwns(AuthServiceProvider::ROLE_COMPANY, $company, $laratrust);

        if (empty($company->applicationData)) {
            Log::error($company->id . ' company was an orphan. No application data found in NoSql. The system will delete a company.');
        } else {
            $company->applicationData->delete();
        }

        if (empty($company->defaultData)) {
            Log::error($company->id . ' company was an orphan. No default data found in NoSql. The system will delete a user.');
        } else {
            $company->defaultData->delete();
        }

        $company->delete();

        return response(null, 204);
    }


}
