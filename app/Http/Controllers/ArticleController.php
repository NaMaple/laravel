<?php

namespace App\Http\Controllers;

//use App\Article;
//use App\Http\Resources\Product as ArticleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ArticleController extends Controller
{
    const VOTE_SCORE = 432;

    public function add(Request $request)
    {
        $data = $request->post();
        $data = $data + array('votes' => 1, 'time' => TIMESTAMP);
        var_dump($data);

        /*
         * 新增文章信息
         * redis hash
         * HSET laravel_demo_redis:article:1 title Laravel之Redis模块操作(全) link https://blog.csdn.net/h330531987/article/details/79090555 poster user:1 time 1590564249 votes 1
         * HSET laravel_demo_redis:article:2 title phpstorm：如何设置代码自动换行 link https://blog.csdn.net/whq19890827/article/details/51206357 poster user:1 time 1590565044 votes 1
         */

        /*
         * 获取最新的文章ID
         * SET article: 0
         */
//        Redis::set(':article:id:', 0);
//        $articleId = Redis::get(':article:id:');
//        var_dump($articleId);die;
        if (empty(Redis::get(':article:id:'))) Redis::set(':article:id:', 0);

        /**
         * 事务实现
         */
        // 自增文章ID
        $articleId = Redis::incr(':article:id:');
        $articleIdKey = 'article:' . $articleId;
        // 自增文章信息
        Redis::hmset(':article:' . $articleId, $data);
        // 新增文章发布时间信息（zset）
        Redis::zadd(':article:time', TIMESTAMP, $articleIdKey);
        // 新增文章评分信息（zset）
        Redis::zadd(':article:score', 0, $articleIdKey);
        // 新增文章投票人员信息（set）
        Redis::sadd(':article:voted:' . $articleId, $data['poster']);
    }

    public function vote(Request $request)
    {
        $data = $request->post();
        $userId = 'user:' . $data['user'];
        $articleId = 'article:' . $data['article'];
        $articleTime = Redis::zscore(':article:time', $articleId);

        // 过期一周
        if ((TIMESTAMP - $articleTime) > ONE_WEEK_SECOND) return false;

        // 文章评分加VOTE_SCORE
        Redis::zincrby(':article:score', self::VOTE_SCORE, $articleId);

        // 文章访问人
        Redis::sadd(':article:voted:' . $data['article'], $userId);

        // 文章访问
        Redis::hincrby(':article:'.$data['article'], 'votes', 1);
    }

    public function getArticle(Request $request){
        $data = $request->post();
        $page = $data['page'];
        $order = $data['order'];
        $pageSize = $data['page_size'];
//        extract($data);

        $start = ($page - 1) * $pageSize;
        $end = $start + $pageSize - 1;

        $ids = Redis::zrevrange(':article:'.$order, $start, $end);

        $data = array();
        foreach ($ids as $k=>$v){
            // 获取文章信息
            $article = Redis::hgetall(':'.$v);
            array_push($data, $article);
        }
        var_dump($data);
    }
}
