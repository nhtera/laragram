<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\SiteConfigurationRepositoryInterface;
use App\Http\Requests\Admin\SiteConfigurationRequest;
use App\Http\Requests\PaginationRequest;

class SiteConfigurationController extends Controller
{

    /** @var \App\Repositories\SiteConfigurationRepositoryInterface */
    protected $siteConfigurationRepository;


    public function __construct(
        SiteConfigurationRepositoryInterface $siteConfigurationRepository
    )
    {
        $this->siteConfigurationRepository = $siteConfigurationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\PaginationRequest $request
     * @return \Response
     */
    public function index(PaginationRequest $request)
    {
        $offset = $request->offset();
        $limit = $request->limit();
        $count = $this->siteConfigurationRepository->count();
        $models = $this->siteConfigurationRepository->get('id', 'desc', $offset, $limit);

        return view('pages.admin.site-configurations.index', [
            'models'  => $models,
            'count'   => $count,
            'offset'  => $offset,
            'limit'   => $limit,
            'baseUrl' => \URL::action('Admin\SiteConfigurationController@index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Response
     */
    public function create()
    {
        return view('pages.admin.site-configurations.edit', [
            'isNew'             => true,
            'siteConfiguration' => $this->siteConfigurationRepository->getBlankModel(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return \Response
     */
    public function store(SiteConfigurationRequest $request)
    {
        $input = $request->only([
            'id',
            'locale',
            'name',
            'title',
            'keywords',
            'description',
            'ogp_image_id',
            'twitter_card_image_id',
        ]);
        $model = $this->siteConfigurationRepository->create($input);

        if (empty( $model )) {
            return redirect()->back()->withErrors(\Lang::get('admin.errors.general.save_failed'));
        }

        return redirect()->action('Admin\SiteConfigurationController@index')->with('message-success',
            \Lang::get('admin.messages.general.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Response
     */
    public function show($id)
    {
        $model = $this->siteConfigurationRepository->find($id);
        if (empty( $model )) {
            \App::abort(404);
        }

        return view('pages.admin.site-configurations.edit', [
            'isNew'             => false,
            'siteConfiguration' => $model,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param      $request
     * @return \Response
     */
    public function update($id, SiteConfigurationRequest $request)
    {
        /** @var \App\Models\SiteConfiguration $model */
        $model = $this->siteConfigurationRepository->find($id);
        if (empty( $model )) {
            \App::abort(404);
        }
        $input = $request->only([
            'id',
            'locale',
            'name',
            'title',
            'keywords',
            'description',
            'ogp_image_id',
            'twitter_card_image_id',
        ]);
        $this->siteConfigurationRepository->update($model, $input);

        return redirect()->action('Admin\SiteConfigurationController@show', [$id])->with('message-success',
            \Lang::get('admin.messages.general.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Response
     */
    public function destroy($id)
    {
        /** @var \App\Models\SiteConfiguration $model */
        $model = $this->siteConfigurationRepository->find($id);
        if (empty( $model )) {
            \App::abort(404);
        }
        $this->siteConfigurationRepository->delete($model);

        return redirect()->action('Admin\SiteConfigurationController@index')->with('message-success',
            \Lang::get('admin.messages.general.delete_success'));
    }

}
