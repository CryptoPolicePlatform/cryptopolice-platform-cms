<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityComment;

class PostComments extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts list',
            'description' => 'Community Posts List'
        ];
    }

    public function setImagePath($diskName)
    {
        return '..\\storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onRun()
    {
        $comments = Db::table('cryptopolice_platform_community_comment as comment')
            ->join('users','comment.user_id', 'users.id')
            ->leftJoin('system_files', function ($join) {
                $join->on('comment.user_id', '=', 'system_files.attachment_id')
                    ->where('system_files.attachment_type', 'RainLab\User\Models\User');
            })
            ->select('system_files.disk_name as user_image', 'comment.*', 'users.nickname', 'users.email')
            ->where('comment.post_id', $this->param('id'))
            ->orderBy('comment.created_at', 'desc')
            ->get();

        foreach ($comments as $key => $value) {
            if ($value->user_image) {
                $comments[$key]->user_image = $this->setImagePath($value->user_image);
            }
        }
        $this->page['comments'] = $comments;
    }

    public function onAddComment()
    {

        $rules = [
            'description' => 'required|min:0|max:10000'
        ];

        $validator = Validator::make(input(), $rules);

        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        } else {

            $user = Auth::getUser();

            $comment = new CommunityComment;
            $comment->user_id = $user->id;
            $comment->post_id = $this->param('id');
            $comment->description = input('description');
            $comment->save();

            Flash::success('Your comment is add');
            return redirect()->back();
        }
    }
}