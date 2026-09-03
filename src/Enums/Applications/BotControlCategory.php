<?php

declare(strict_types=1);

namespace CommunitySDKs\LaravelCloud\Enums\Applications;

/** Values accepted by the Laravel Cloud API for BotControlCategory. */
enum BotControlCategory: string
{
    case AcademicResearch = 'academic_research';
    case Accessibility = 'accessibility';
    case AdvertisingAndMarketing = 'advertising_and_marketing';
    case Aggregator = 'aggregator';
    case AiAssistant = 'ai_assistant';
    case AiCrawler = 'ai_crawler';
    case AiSearch = 'ai_search';
    case FeedFetcher = 'feed_fetcher';
    case MonitoringAndAnalytics = 'monitoring_and_analytics';
    case PagePreview = 'page_preview';
    case SearchEngineCrawler = 'search_engine_crawler';
    case SearchEngineOptimization = 'search_engine_optimization';
    case Security = 'security';
    case SocialMediaMarketing = 'social_media_marketing';
    case Webhooks = 'webhooks';
    case Other = 'other';
}
