# Laravel-Zoom API

A Laravel application to serve Zoom API
endpoints to manage meetings. See the documentation
[here](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#tag/Meetings)

## Getting Started

1. Create a developer account in [Zoom](https://developers.zoom.us/)
2. Sign in if sign up did not sign you in
3. Click on `Build App`
4. Choose `JWT` in choose your app type
5. Fill in required information
6. Copy the value for `API Key` and paste to `ZOOM_CLIENT_KEY` in `.env` and `API Secret` to `ZOOM_CLIENT_SECRET`

After these changes, you can now use the application. In the frontend, you may use [axios](https://github.com/axios/axios) to make the requests.
You can also test the application using [Postman](https://postman.com).

A demo of this app is available at [ZoomAPI](https://my-zoom-api.herokuapp.com).

## Usage

This API has four endpoints:

GET `/api/zoom/meetings`     returns a paginated list of meetings
[documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetings)

Arguments:

| query param | default | description                        |
|-------------|---------|------------------------------------|
| page_size   |    10   | Number of records to show per page |
| page_number |    1    | Page number to retrieve |
| next_page_token | null | Shown when available results exceeds page size |
| type        | scheduled | Type of meeting to return, accepts: `live`, `scheduled`, `upcoming`, `upcoming_meetings`, `previous_meetings` |

Returns: https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetings

POST `/api/zoom/meetings`    creates a new meeting
[documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetings)

Arguments:

| arg | default | required  | description
|-----|---------|-----------|------------
|topic| null    | yes       | The title of the meeting
|duration| 60   | yes       | Duration of meeting in minutes
|password| null | no        | Custom password; if not set, zoom will generate one 
|start_time| null | yes     | Time the meeting will start
|agenda| null   | no        | The description of the meeting
|meeting_invitees| null | yes | Email of invitees. Must follow format: [{email:"email@example.com"}]
|type | 2       | yes       | Type of meeting to create: 1 - instant, 2 - scheduled, 3 - recurring with no fixed time, 8 - recurring with fixed time

Returns: https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetingCreate

GET `/api/zoom/meetings/{meetingId}` shows meeting information
[documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meeting)

Arguments:

| arg     | default | description |
|---------|---------|-------------|
|meetingId|null     | Must be passed as part of the URL

Response: [Meeting object](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meeting)

PUT `/api/zoom/meetings/{meetingId}` updates meeting information
[documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetingUpdate)

Arguments:

| arg     | default | description |
|---------|---------|-------------|
|meetingId| null    | Must be passed as part of the url
|topic| null    | yes       | The title of the meeting
|duration| 60   | yes       | Duration of meeting in minutes
|password| null | no        | Custom password; if not set, zoom will generate one 
|start_time| null | yes     | Time the meeting will start
|agenda| null   | no        | The description of the meeting
|meeting_invitees| null | yes | Email of invitees. Must follow format: [{email:"email@example.com"}]
|type | 2       | yes       | Type of meeting to create: 1 - instant, 2 - scheduled, 3 - recurring with no fixed time, 8 - recurring with fixed time

DELETE `/api/zoom/meetings/{meetingId}`  deletes meeting
[documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api/methods#operation/meetingDelete)

Arguments:

| arg     | default | description |
|---------|---------|-------------|
|meetingId| null    | Must be passed as part of the url

Returns: see documentation
