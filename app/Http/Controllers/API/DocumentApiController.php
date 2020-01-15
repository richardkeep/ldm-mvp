<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreDocument;
use App\Http\Resources\Document as DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDocument;
use App\Services\FileService;
use Storage;

class DocumentApiController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $lawsuit
     * @param Document $document
     * @return \Illuminate\Http\Response
     */
    public function show($lawsuit, $document)
    {
        $document = Document::where('id', $document)->where('lawsuit_id', $lawsuit)->first();
        return response([
            'data' => new DocumentResource($document)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDocument $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDocument $request)
    {
        $data = $request->all();
        // storage file on aws-s3
        $data['url'] = $this->fileService->createFileS3($request, 'file');

        Document::create($data);

        $message = ['status' => 'success', 'content' => 'ファイルをアップロードしました。'];
        return response()->json([
            'url' => route('lawsuits.show', $data['lawsuit_id']),
            'message' => $message
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDocument $request
     * @param Document $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateDocument $request, Document $document)
    {
        $data = $request->only(['name', 'number', 'type_document_id', 'submitter_id', 'created_at']);
        $document->updated_at = now();
        $document->fill($data)->save();

        $message = ['status' => 'success', 'content' => '文書が正常に更新しました。'];
        $url = $document->submitter_id == 1 || $document->submitter_id == 3 ?
            route('documents.index', [$document->lawsuit_id, $document->submitter->description]) :
            route('lawsuits.show', $data['lawsuit_id']);

        return response()->json([
            'url' => $url,
            'message' => $message
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $document->delete();

        $url = $document->submitter_id == 1 || $document->submitter_id == 3 ?
            route('documents.index', [$document->lawsuit_id, $document->submitter->description]) :
            route('lawsuits.show', $document['lawsuit_id']);

        $message = ['status' => 'success', 'content' => 'ドキュメントは削除されました。'];
        return response()->json(['url' => $url, 'message' => $message], 200);
    }
}
