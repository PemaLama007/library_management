<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('publisher.index', [
            'publishers' => Publisher::Paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('publisher.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePublisherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePublisherRequest $request)
    {
        Publisher::create($request->validated());
        return redirect()->route('publishers');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\\Models\\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show(publisher $publisher)
    {
        return view('publisher.show', [
            'publisher' => $publisher
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\\Models\\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function edit(publisher $publisher)
    {
        return view('publisher.edit', [
            'publisher' => $publisher
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePublisherRequest  $request
     * @param  \App\\Models\\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePublisherRequest $request, $id)
    {
        $publisher = Publisher::find($id);
        $publisher->name = $request->input('name');
        $publisher->save();

        return redirect()->route('publishers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Publisher::find($id)->delete();
        return redirect()->route('publishers');
    }
}
