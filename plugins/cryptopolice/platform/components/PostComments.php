<?php namespace CryptoPolice\Platform\Components;

use CryptoPolice\Platform\Models\CommunityPost;
use DB;
use Auth;
use Flash;
use Session;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityComment;
use CryptoPolice\Academy\Components\Recaptcha as Recaptcha;


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
        return '\\storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onRun()
    {
        $comments = Db::table('cryptopolice_platform_community_comment as comment')
            ->join('users', 'comment.user_id', 'users.id')
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
        $this->page['comments'] = $this->makeArrayTree($comments);
    }


    private function makeArrayTree($comments)
    {

        $childs = [];

        foreach ($comments as $comment) {
            $childs[$comment->parent_id][] = $comment;
        }

        foreach ($comments as $comment) {
            if (isset($childs[$comment->id])) {
                $comment->childs = $childs[$comment->id];
            }
        }

        count($childs) > 0 ? $tree = $childs[0] : $tree = [];
        return $tree;
    }


    public function onAddComment()
    {

        if (input('_token') == Session::token()) {

            if ($this->checkLinks(input('description'))) {
                Flash::error('Links are not allowed');
            } else {

                $user = Auth::getUser();

                $comment = new CommunityComment;
                $comment->user_id = $user->id;
                $comment->post_id = $this->param('id');
                $comment->description = input('description');
                if (!empty(input('parent_id'))) {
                    $comment->parent_id = input('parent_id');
                }
                $comment->save();

                $this->increasePostsCommentsCount($this->param('id'));

                Flash::success('Your comment has been successfully added');
                return redirect()->back();
            }
        }
    }

    public function increasePostsCommentsCount($id)
    {
        return CommunityPost::find($id)->increment('comment_count');
    }

    public function decreasePostsCommentsCount($id)
    {
        return CommunityPost::find($id)->decrement('comment_count');
    }

    public function checkLinks($value)
    {
        preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $value, $result, PREG_PATTERN_ORDER);
        return $result[0];
    }

    public function onDeleteComment()
    {

        $user = Auth::getUser();

        if ($user && !empty(post('id'))) {
            DB::table('cryptopolice_platform_community_comment')->where('user_id', $user->id)->where('id', post('id'))->delete();
            Flash::warning('Your comment has been successfully deleted');
            $this->decreasePostsCommentsCount($this->param('id'));
            return redirect()->back();
        }
    }
}