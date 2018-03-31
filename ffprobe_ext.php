<?php
class ffprobe_ext extends ffprobe
{
    public static function getVideoInfo($filename)
    {
    	$info = new ffprobe($filename);
        $stream = $info->streams[0];
        $format = $info->format;
        $info   = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $info->duration     = (float) $stream->duration;
        $info->frame_height = (int) $stream->height;
        $info->frame_width  = (int) $stream->width;
        eval("\$frame_rate = {$stream->r_frame_rate};");
        eval("\$avg_frame_rate = {$stream->avg_frame_rate};");
        $info->frame_rate   = (float) $frame_rate;
        $info->avg_frame_rate = (float) $avg_frame_rate;
        $info->size = (float)$format->size;
        return $info;
    }
	public static function getMusicInfo($filename)
    {
    	$info = new ffprobe($filename);
        $stream = $info->streams[0];
        $format = $info->format;
        $info   = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $info->duration     = (float) $stream->duration;
        $info->size = (float)$format->size;
        return $info;
    }
}
?>