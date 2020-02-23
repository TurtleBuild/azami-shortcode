<?php
/*-------------------------------------------------
Plugin Name: AZAMI Shortcode
Plugin URI: https://github.com/TurtleBuild/azami-shortcode
Description: WordPressテーマ「AZAMI」用のショートコードです。
Author: Masahiro Kasatani
License: GPLv2
-------------------------------------------------*/

add_action( 'wp_enqueue_scripts', function() {
  if( is_admin() ) { return; }
  $cssurl = plugins_url('style.css', __FILE__);
  wp_enqueue_style('azami-shortcode', $cssurl);
});

//登録
add_shortcode('youtube', 'responsive_youtube');
add_shortcode('googlemap', 'responsive_googlemap');
add_shortcode('listbox', 'azami_list_box');
add_shortcode('memobox', 'azami_memo_box');
add_shortcode('blockquote', 'azami_blockquote');
add_shortcode('code', 'azami_source_code');
add_shortcode('col', 'azami_column');
add_shortcode('grid', 'azami_grid');
add_shortcode('reference', 'azami_reference_site');
add_shortcode('card', 'azami_card');

/*-------------------------------------------------
YouTubeをレスポンシブで挿入
-------------------------------------------------*/
if ( !function_exists('responsive_youtube') ) {
  function responsive_youtube($atts, $content = null)
  {
    if ($content) {
      return '<div class="embed-responsive embed-responsive-16by9 my-3">' . $content . '</div>';
    }

  }
}

/*-------------------------------------------------
GoogleMapをレスポンシブで挿入
-------------------------------------------------*/
if ( !function_exists('responsive_googlemap') ) {
  function responsive_googlemap($atts, $content = null)
  {
    if ($content) {
      return '<div class="embed-responsive embed-responsive-4by3 my-3">' . $content . '</div>';
    }

  }
}

/*-------------------------------------------------
リストボックス
-------------------------------------------------*/
if ( !function_exists('azami_list_box') ) {
  function azami_list_box($atts, $content = null)
  {
    if ($content) {
      $search = array('<p>','</p>');
      $content = str_replace($search,'',$content);
      $title = ( isset($atts['title']) ) ? '<div class="list-box-title">' . esc_attr($atts['title']) . '</div>' : null;
      $class = isset($atts['class']) ? esc_attr($atts['class']) : null;
      return '<div class="list-box p-3 my-5 mx-md-5 mx-0 ' . $class . '">' . $title . $content . '</div>';
    }
  }
}

/*-------------------------------------------------
メモボックス
-------------------------------------------------*/
if ( !function_exists('azami_memo_box') ) {
  function azami_memo_box($atts, $content = null)
  {
    $title = isset($atts['title']) ? '<div class="memo-box-title pb-2"> ' . esc_attr($atts['title']) . '</div>' : '';
    $class = isset($atts['class']) ? esc_attr($atts['class']) : null;
    if ($content) {
      $content = do_shortcode( shortcode_unautop($content) );
      $output = <<<EOF
<div class="memo-box p-3 mb-5 {$class}">{$title}{$content}</div>
EOF;
      return $output;
    }
  }
}

/*-------------------------------------------------
引用
-------------------------------------------------*/
if ( !function_exists('azami_blockquote') ) {
  function azami_blockquote($atts, $content = null)
  {
    if ($content) {
      $cite = ( isset($atts['cite']) ) ? esc_attr($atts['cite']) : null;
      $content = str_replace('<br />', '', $content);
      return '<blockquote><p>' . $content . '</p><p><cite>' . $cite . '</cite></p></blockquote>';
    }
  }
}

/*-------------------------------------------------
ソースコード
-------------------------------------------------*/
if ( !function_exists('azami_source_code') ) {
  function azami_source_code($atts, $content = null)
  {
    if ($content) {
      $content = str_replace('<br />', '', $content);
      $title = isset($atts['title']) ? esc_attr($atts['title']) : null;
      $lang = ( isset($atts['lang']) ) ? esc_attr($atts['lang']) : null;
      $output = <<<EOF
<pre class="mb-5"><code id="{$title}" class="{$lang}">{$content}</code></pre>
EOF;
      return $output;
    }
  }
}

/*-------------------------------------------------
グリッドのカラム
-------------------------------------------------*/
if ( !function_exists('azami_column') ) {
  function azami_column($atts, $content = null)
  {
    $column = ( isset($atts['num']) ) ? esc_attr($atts['num']) : null;
    if($column == 2 || $column == 3 || $column == 4){
      $column = 12 / $column; // bootstrapのグリッド数によって変更する必要あり
    }
    $content = do_shortcode( shortcode_unautop($content) );
    if ( $content && ($column == 3 || $column == 4 || $column == 6) ) {
      return '<div class="col-'. $column .'">' . $content . '</div>';
    }
  }
}

/*-------------------------------------------------
グリッド
-------------------------------------------------*/
if ( !function_exists('azami_grid') ) {
  function azami_grid($atts, $content = null)
  {
    $content = do_shortcode( shortcode_unautop($content) );
    if ($content) {
      return '<div class="row">' . $content . '</div>';
    }
  }
}

/*-------------------------------------------------
参考サイト
-------------------------------------------------*/
if ( !function_exists('azami_reference_site') ) {
  function azami_reference_site($atts)
  {
    $url = isset($atts['url']) ? esc_url($atts['url']) : null;
    $title = isset($atts['title']) ? esc_attr($atts['title']) : null;
    $site = isset($atts['site']) ? '<span>' . esc_attr($atts['site']) . '</span>' : "";
    if ($url && $title) { // タイトルとURLがある場合のみ出力
        $output = <<<EOF
<div class="reference-site px-3 mb-5" style="max-width: 400px;">
  <a class="row align-items-center" href="{$url}" target="_blank" rel="nofollow noopener noreferrer">
    <div class="col-2 text-center pt-4 pb-2"><i class="fas fa-bookmark"></i><p>参考</p></div>
    <div class="col-10 py-3"><p class="reference-title">{$title}</p><p>{$site}</p></div>
  </a>
</div>
EOF;
      return $output;
    } else {
      return '<span class="red">参考記事のタイトルとURLを入力してください</span>';
    }
  }
}

/*-------------------------------------------------
記事カード
-------------------------------------------------*/
if ( !function_exists('azami_card') ) {
  function azami_card($atts)
  {
    $id = isset($atts['id']) ? esc_attr($atts['id']) : null;
    $output = '';
    if ($id) {
      $ids = ( explode(',', $id) ); //一旦配列に
    }
    if ( isset($ids) ) {
      foreach ($ids as $eachid) {
        $img = '<img src="' . azami_get_the_thumbnail('thumb-640', $eachid) . '" loading="lazy" class="card-img-top">';
        $url = esc_url( get_permalink($eachid) );
        $title = esc_attr( get_the_title($eachid) );
        if ($url && $title) {
            $output .= <<<EOF
<div class="card" style="max-width: 320px;">
  <a href="{$url}">
    <div class="ratio-16to9">{$img}</div>
    <div class="card-body">{$title}</div>
  </a>
</div>
EOF;
        } //endif
      } //endforeach
    } else { $output = '記事IDを正しく入力してください'; }
    return $output;
  }
}


/*-------------------------------------------------
「AZAMI ショートコード」の更新通知
-------------------------------------------------*/
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/TurtleBuild/azami-shortcode/master/azami-shortcode-update.json',
    __FILE__,
    'azami-shortcode'
);