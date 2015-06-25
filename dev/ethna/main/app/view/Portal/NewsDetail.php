<?php
/**
 *  Portal/NewsDetail.php
 *	ニュース詳細
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  portal_newsDetail view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_PortalNewsDetail extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$pp_id = $this->af->getRequestedBasicAuth( 'user' );
		
		$news_id = $this->af->get( "news_id" );
		
		$pnews_m =& $this->backend->getManager( "PortalNews" );
		$ptheme_m =& $this->backend->getManager( "PortalTheme" );
		$puser_m =& $this->backend->getManager( "PortalUser" );
		
		$user = $puser_m->getUserBase( $pp_id );
		$m_theme = $ptheme_m->getMasterThemeList();
		
		$user['theme_name'] = $m_theme[$user['theme_id']]['theme_name'];

		$theme_list = $ptheme_m->getUserThemeList( $pp_id, "db" );
		
		// jsに渡すデータの作成
		$theme_info = array();
		foreach ( $m_theme as $theme_id => $row ) {
			$theme_info[] = array(
				"theme_id"		=> $row['theme_id'],
				"theme_name"	=> $row['theme_name'],
				"use_point"		=> intval( $row['use_point'] ),
				"lock_flg"		=> ( !isset( $theme_list[$theme_id] ) ) ? 1 : 0,
				"selected_flg"	=> ( $theme_id == $user['theme_id'] ) ? 1 : 0,
			);
		}
		$theme_info = htmlspecialchars( json_encode( $theme_info ) );
		
		
		$news = $pnews_m->getNews( $news_id );
		
		// ニュース情報を解析して、指定タグをリンクに変換
		// タグは暫定で{link_a url="xxx"}txt{/link_a}
		$news['news_text'] = html_entity_decode( $news['news_text'] );
		$pattern = '/<linka url={(.*?)}>(.*?)<\/linka>/misu';
		preg_match_all( $pattern, $news['news_text'], $matches );

		// 該当の文字列を置き換える
		$replace = array();
		$link = array();
		$text = array();
		foreach ( $matches as $key => $rows ) {
		    foreach ( $rows as $key2 => $row ) {
		        switch ( $key ) {
		            case 0: // リンク対象文字列全体
		                $replace[$key2] = $row;
		                break;
		                
		            case 1: // URL
		                $link[$key2] = $row;
		                break;
		                
		            case 2: // 文字列
		                $text[$key2] = $row;
		                break;
		        }
		    }
		}

		foreach ( $replace as $key => $row ) {
		    $news['news_text'] = str_replace( $row, '<a onClick="Unity.call(\'' . $link[$key] . '\');">' . $text[$key] . '</a>', $news['news_text'] );
		}
		
		// 投票ポイント関係
		$user_m =& $this->backend->getManager( "User" );
		$voting = $user_m->getUserVoting( $pp_id, "db" );
		$this->af->setApp( "voting", $voting );
		
		$this->af->setApp( "user", $user );
		$this->af->setApp( "news", $news );
		$this->af->setAppNe( "news_text", nl2br( $news['news_text'] ) );
		$this->af->setAppNe( "theme_info", $theme_info );
    }
}
?>