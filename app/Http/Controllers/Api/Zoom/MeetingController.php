<?php

namespace App\Http\Controllers\Api\Zoom;

use App\Http\Controllers\Controller;
use App\Traits\ZoomJWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    use ZoomJWT;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    const PATH = '/users/me/meetings';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $path = '/users/me/meetings';

        $query = [
            'type'              => $request->type ?? 'scheduled',
            'page_size'         => $request->page_size ?? 10,
            'next_page_token'   => $request->next_page_token ?? null,
            'page_number'       => $request->page_number ?? 1
        ];

        $response = $this->zoomGet(self::PATH, $query);

        $data = json_decode($response->body(), true);

        // format the start at information of the meetings
        $data['meetings'] = array_map(function ($m) {
            $m['start_at'] = $this->toUnixTimeStamp($m['start_time'], $m['timezone']);
            return $m;
        }, $data['meetings']);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic'                     => 'required|string',
            'duration'                  => 'required|numeric|gt:0',
            'password'                  => 'required|string',
            'start_time'                => 'required|date',
            'agenda'                    => 'string|nullable',
            'meeting_invitees'          => 'required|array|min:1',
            'meeting_invitees.*.email'  => 'required|email',
            'type'                      => 'required|in:1,2,3,8',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }

        $data = $validator->validated();

        $response = $this->zoomPost(self::PATH, [
            'topic' => $data['topic'],
            'type' => $data['type'],
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration' => $data['duration'],
            'agenda' => $data['agenda'],
            'password' => $data['password'],
            'settings' => [
                'host_video'        => true,
                'participant_video' => false,
                'waiting_room'      => true,
                'meeting_invitees'  => $data['meeting_invitees'],
                'mute_upon_entry'   => true,
                'private_meeting'   => true,
                'registrants_email_notification'=> true,
                'registrants_confirmation_email'=> true,
                'meeting_authentication' => true,
            ]
        ]);

        return [
            'success' => $response->status() === 201,
            'data'    => json_decode($response->body(), true),
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $path = '/meetings/' . $id;

        $response = $this->zoomGet($path);

        return [
            'success' => $response->status() === 200,
            'data' => json_decode($response->body(), true),
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'topic'                     => 'required|string',
            'duration'                  => 'required|numeric|gt:0',
            'password'                  => 'required|string',
            'start_time'                => 'required|date',
            'agenda'                    => 'string|nullable',
            'meeting_invitees'          => 'required|array|min:1',
            'meeting_invitees.*.email'  => 'required|email',
            'type'                      => 'required|in:1,2,3,8',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }

        $data = $validator->validated();

        $response = $this->zoomPatch('/meetings/' . $id, [
            'topic' => $data['topic'],
            'type' => $data['type'],
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration' => $data['duration'],
            'agenda' => $data['agenda'],
            'password' => $data['password'],
            'settings' => [
                'host_video'        => true,
                'participant_video' => false,
                'waiting_room'      => true,
                'meeting_invitees'  => $data['meeting_invitees'],
                'mute_upon_entry'   => true,
                'private_meeting'   => true,
                'registrants_email_notification'=> true,
                'registrants_confirmation_email'=> true,
                'meeting_authentication' => true,
            ]
        ]);

        return [
            'success' => $response->status() === 204,
            'data'    => json_decode($response->body(), true),
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $path = '/meetings/' . $id;

        $response = $this->zoomDelete($path);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
    }
}
