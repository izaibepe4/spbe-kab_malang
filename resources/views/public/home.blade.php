@extends('layouts.public')

@section('title', 'Beranda - SPBE Kabupaten Malang')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    /* Global Poppins Font */
    body,
    * {
        font-family: 'Poppins', sans-serif;
    }

    /* Font Weight Defaults */
    p,
    span,
    div,
    a,
    li,
    td,
    th {
        font-weight: 400;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .font-bold,
    .font-semibold {
        font-weight: 600;
    }

    .font-bold,
    h1,
    h2,
    .section-title {
        font-weight: 700;
    }

    /* Hero Section Background */
    .hero-bg {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        position: relative;
    }

    .hero-slider {
        position: relative;
        width: 100%;
        min-height: 100vh;
        overflow: hidden;
        max-width: 100%;
        z-index: 1;
        isolation: isolate;
    }

    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        z-index: 1;
        pointer-events: none;
    }

    .hero-slide.active {
        opacity: 1;
        z-index: 2;
        pointer-events: auto;
    }

    .hero-slide-content {
        position: relative;
        z-index: 3;
        height: 100vh;
        display: flex;
        align-items: center;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.8s ease-in-out 0.4s, visibility 0s linear 0.8s;
    }

    .hero-slide.active .hero-slide-content {
        opacity: 1;
        visibility: visible;
        transition: opacity 0.8s ease-in-out 0.4s, visibility 0s linear 0s;
    }

    .hero-slide.active .hero-slide-content * {
        pointer-events: auto;
    }

    .hero-slide-content.left {
        justify-content: flex-start;
    }

    .hero-slide-content.center {
        justify-content: center;
        text-align: center;
    }

    .hero-slide-content.right {
        justify-content: flex-end;
        text-align: right;
    }

    .hero-slider-controls {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        display: flex;
        gap: 12px;
        pointer-events: auto;
    }

    .hero-slider-dot {
        width: 40px;
        height: 8px;
        border-radius: 0;
        background: rgba(100, 100, 100, 0.6);
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .hero-slider-dot.active {
        background: #FFA84A;
        width: 80px;
    }

    .hero-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        pointer-events: auto;
    }

    .hero-slider-nav:hover {
        background: rgba(255, 168, 74, 0.8);
        border-color: #FFA84A;
    }

    .hero-slider-nav.prev {
        left: 30px;
    }

    .hero-slider-nav.next {
        right: 30px;
    }

    /* Override section padding untuk hero */
    .home-hero-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Ensure body allows full width and prevent horizontal overflow */
    html {
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
    }

    body {
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
        position: relative;
    }

    /* Prevent all sections from causing overflow */
    section {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Prevent overflow issues in chart containers */
    .progress-content-box,
    .progress-content-box>div,
    .progress-content-box #spbeIndexChart,
    .progress-card,
    .progress-wrapper {
        box-sizing: border-box;
    }

    /* Shared card accents */
    :root {
        --card-border-color: rgba(16, 42, 102, 0.12);
        --card-shadow: 0 18px 40px -20px rgba(16, 42, 102, 0.35);
        --card-shadow-strong: 0 28px 60px -20px rgba(16, 42, 102, 0.45);
    }

    /* Hero Overlay */
    .hero-overlay {
        background: rgba(0, 0, 0, 0.4);
    }

    /* Floating WhatsApp Button */
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .whatsapp-float:hover {
        background-color: #20ba5a;
        transform: scale(1.1);
    }

    /* Floating Helpdesk Button */
    .helpdesk-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 110px;
        right: 40px;
        background-color: #1c2b78;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .helpdesk-float:hover {
        background-color: #15205a;
        transform: scale(1.1);
    }

    /* Helpdesk Chat Widget (optional - bisa dikembangkan) */
    .helpdesk-widget {
        position: fixed;
        bottom: 180px;
        right: 40px;
        width: 320px;
        max-height: 400px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 99;
        display: none;
        overflow: hidden;
    }

    .helpdesk-widget.active {
        display: block;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Section Styles */
    .section-title {
        font-size: 32px;
        font-weight: 700;
        color: #102A66;
        margin-bottom: 8px;
    }

    .section-subtitle {
        font-size: 16px;
        color: #64748B;
        margin-bottom: 32px;
    }

    .section-bg-dark {
        background: linear-gradient(135deg, #102A66 0%, #1C2B78 100%);
        color: white;
        padding: 60px 0;
    }

    .section-bg-dark .section-title {
        color: #FFA84A;
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .section-bg-dark .section-subtitle {
        color: white;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 32px;
    }

    .section-content-area {
        background: #ECF3FE;
        padding: 40px 0;
        margin-top: 0;
    }

    .section-heading {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        position: relative;
    }

    .section-heading--stacked {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .section-heading__eyebrow {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #FFA84A;
        letter-spacing: 0.01em;
    }

    .section-heading__title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 24px;
        color: #FFFFFF;
    }

    .progress-pagination {
        display: flex;
        align-items: center;
        gap: 12px;
        position: absolute;
        right: 0;
        top: 60%;
        transform: translateY(-50%);
    }

    .progress-pagination-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .progress-pagination-btn:hover {
        background: #FFA84A;
        box-shadow: 0 4px 12px rgba(255, 168, 74, 0.3);
    }

    .progress-pagination-btn:hover svg {
        stroke: #ffffff;
    }

    .progress-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .progress-pagination-btn:disabled:hover {
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .progress-pagination-btn:disabled:hover svg {
        stroke: #000000;
    }

    .progress-pagination-btn svg {
        width: 24px;
        height: 24px;
        stroke: #000000;
        transition: stroke 0.3s ease;
    }

    .progress-slider-wrapper {
        position: relative;
        overflow: hidden;
        padding: 6px 0;
    }

    .progress-slider-container {
        display: flex;
        transition: transform 0.5s ease;
        will-change: transform;
    }

    .progress-slide {
        min-width: 100%;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 32px;
    }

    @media (max-width: 1280px) {
        .progress-slide {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .progress-slide {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .section-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        .progress-pagination {
            position: static;
            transform: none;
            justify-content: flex-start;
            margin-top: 16px;
        }
    }

    .dual-tone-section {
        position: relative;
        width: 100vw;
        max-width: 100%;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        overflow: hidden;
        left: 0;
        right: 0;
    }

    @supports (width: 100dvw) {
        .dual-tone-section {
            width: 100dvw;
        }
    }

    @media (max-width: 768px) {
        .dual-tone-section {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }
    }

    .dual-tone-section__top {
        background: #11286e;
        padding: 110px 0 240px;
    }

    .dual-tone-section__bottom {
        background: #FFFFFF;
        padding: 170px 0 90px;
    }

    .dual-tone-section--progress .dual-tone-section__bottom {
        background: #FFFFFF;
    }

    .dual-tone-section .section-title,
    .dual-tone-section .section-subtitle {
        color: #ffffff;
    }

    .cards-floating {
        margin-top: -320px;
        position: relative;
    }

    .dual-tone-section--indikator .cards-floating {
        margin-top: -360px;
    }

    .dual-tone-section--indikator .progress-wrapper {
        align-items: flex-start;
    }

    .dual-tone-section--indikator .progress-title-box {
        margin-left: 0;
        margin-bottom: -20px;
        width: auto;
        min-width: 220px;
        max-width: 90%;
        box-shadow: 0 6px 16px -8px rgba(16, 42, 102, 0.35);
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .dual-tone-section {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .dual-tone-section__top {
            padding: 64px 0 190px;
        }

        .dual-tone-section__bottom {
            padding: 160px 0 60px;
        }

        .cards-floating {
            margin-top: -190px;
        }
    }

    /* Card Styles */
    .feature-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--card-shadow-strong);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        margin-bottom: 20px;
        color: #FFA84A;
    }

    .news-card {
        background: white;
        border-radius: 12px;
        padding: 0;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .news-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--card-shadow-strong);
    }

    .news-card-content {
        padding: 12px 18px 18px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .news-card-footer {
        padding: 0 18px 18px 18px;
        display: flex;
        align-items: flex-start;
    }

    .news-image-wrapper {
        position: relative;
        overflow: hidden;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .news-image-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0) 60%);
        pointer-events: none;
    }

    .news-category-tag {
        position: absolute;
        top: 16px;
        left: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #102A66;
        color: white;
        padding: 6px 16px;
        border-top-left-radius: 8px;
        border-top-right-radius: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(16, 42, 102, 0.25);
        z-index: 2;
        transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
    }

    .news-card:hover .news-category-tag {
        background: #FFA84A;
        color: #102A66;
        transform: translateY(-2px);
    }

    .news-title {
        font-size: 14px;
        font-weight: 700;
        color: #102A66;
        margin-bottom: 4px;
        line-height: 1.4;
        transition: color 0.3s ease;
        text-align: center;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .news-card:hover .news-title {
        color: #FFA84A;
    }

    .news-title a {
        color: inherit;
        text-decoration: none;
    }

    .news-title a:hover {
        color: #FFA84A;
    }

    .news-date {
        font-size: 12px;
        color: #5f6b94;
        margin-bottom: 0;
        margin-top: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-date svg {
        color: #FFA84A;
    }

    .news-meta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        color: #5f6b94;
        margin-top: 8px;
    }

    .news-meta-separator {
        color: #cbd5e1;
    }

    .news-read-article {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #5f6b94;
        text-decoration: none;
        font-size: 12px;
        transition: color 0.3s ease;
    }

    .news-read-article:hover {
        color: #102A66;
    }

    .news-read-article svg {
        width: 14px;
        height: 14px;
        color: #FFA84A;
    }

    .news-read-article:hover svg {
        color: #102A66;
    }

    .news-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        margin: 0;
        display: block;
        flex-shrink: 0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .news-readmore {
        color: #008BF9;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: auto;
    }

    .news-readmore:hover {
        text-decoration: underline;
    }

    .btn-load-more {
        background: #FFA84A;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
        text-decoration: none;
        border: none;
    }

    .btn-load-more:hover {
        background: #FF9A1F;
    }

    .progress-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        width: 100%;
        max-width: 100%;
        overflow: visible;
        transition: transform 0.3s ease;
        will-change: transform;
    }

    .progress-wrapper:hover {
        text-decoration: none;
        color: inherit;
    }

    .progress-wrapper:hover .progress-title-box {
        transform: translateY(-6px) !important;
        background: #FFA84A !important;
        color: #ffffff !important;
        box-shadow: 0 8px 20px -8px rgba(16, 42, 102, 0.4) !important;
    }

    .progress-wrapper:hover .progress-card {
        transform: translateY(-6px) !important;
        box-shadow: 0 12px 30px -12px rgba(16, 42, 102, 0.3) !important;
    }

    .progress-title-box {
        background: #FFFFFF;
        color: #102A66;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        border-radius: 0;
        z-index: 10;
        position: relative;
        width: fit-content;
        max-width: 90%;
        margin-bottom: -20px;
        box-shadow: 0 12px 30px -18px rgba(16, 42, 102, 0.65);
        transition: background 0.25s ease, color 0.25s ease, transform 0.3s ease, box-shadow 0.3s ease;
        transform: translateY(0);
        will-change: transform;
    }

    .progress-card {
        background: white;
        border-radius: 0;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
        width: 100%;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        aspect-ratio: 1 / 1;
        transform: translateY(0);
        will-change: transform;
    }

    .progress-content-box {
        flex: 1;
        padding: 0;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
        height: 100%;
        aspect-ratio: 1 / 1;
        min-height: 320px;
        margin: 0;
        overflow: hidden;
    }

    .dual-tone-section--indikator .progress-content-box {
        aspect-ratio: 16 / 9;
        min-height: 240px;
    }

    .dual-tone-section--indikator .progress-card {
        aspect-ratio: 16 / 9;
    }


    .progress-content-box>div {
        width: 100%;
        height: 100%;
        max-width: 100%;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }

    .progress-content-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        margin: 0;
        padding: 0;
    }

    .progress-content-box #spbeIndexChart {
        width: 100% !important;
        max-width: 100%;
        min-height: 240px;
        max-height: 320px;
        overflow: hidden;
    }

    /* ApexCharts marker customization */
    #spbeIndexChart .apexcharts-series .apexcharts-marker {
        pointer-events: auto !important;
        transition: fill 0.2s ease, stroke 0.2s ease;
        fill: #1C2B78 !important;
        stroke: #ffffff !important;
        filter: none !important;
    }

    #spbeIndexChart .apexcharts-series .apexcharts-marker:hover,
    #spbeIndexChart .apexcharts-series .apexcharts-marker:focus {
        fill: #FFA84A !important;
        stroke: #FFA84A !important;
        filter: none !important;
    }

    #spbeIndexChart .apexcharts-series .apexcharts-marker.apexcharts-active {
        fill: #FFA84A !important;
        stroke: #FFA84A !important;
        filter: none !important;
    }

    @media (max-width: 768px) {
        .progress-content-box {
            aspect-ratio: 1 / 1;
            min-height: 260px;
        }

        .dual-tone-section--indikator .progress-content-box {
            aspect-ratio: 4 / 3;
            min-height: 200px;
        }

        .progress-content-box #spbeIndexChart {
            width: 100% !important;
            max-width: 100%;
            min-height: 200px;
            max-height: 280px;
        }
    }

    /* Button Styles */
    .btn-primary {
        background: #FFA84A;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #FF9A1F;
    }

    .btn-secondary {
        background: white;
        color: #102A66;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 2px solid #102A66;
    }

    .btn-secondary:hover {
        background: #102A66;
        color: white;
    }

    /* Chart Placeholder */
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
    }

    /* Indicator Placeholder */
    .indicator-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
    }

    /* Indicator Stats Styles */
    .indicator-stats {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0;
        position: relative;
        overflow: hidden;
    }

    .indicator-stats-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 32px 24px;
        gap: 40px;
    }

    .indicator-stats-top {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        align-items: start;
    }

    .indicator-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .indicator-stat-number {
        display: flex;
        align-items: baseline;
        gap: 4px;
        font-family: 'Poppins', sans-serif;
        position: relative;
    }

    .indicator-stat-value {
        font-size: 56px;
        font-weight: 700;
        color: #1C2B78;
        line-height: 1;
        padding-top: 4px;
    }

    .indicator-stat-square {
        width: 10px;
        height: 10px;
        background: #FFA84A;
        border-radius: 50%;
        flex-shrink: 0;
        align-self: flex-end;
        margin-bottom: 0;
    }

    .indicator-stat-label {
        font-size: 14px;
        font-weight: 400;
        color: #FFA84A;
        line-height: 1.4;
        margin-top: 4px;
    }

    .indicator-stats-bottom {
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
        margin-left: calc(16.666% - 8%);
        padding-left: 0;
    }

    .indicator-layanan-heading {
        font-size: 18px;
        font-weight: 700;
        color: #1C2B78;
        font-family: 'Poppins', sans-serif;
        margin-bottom: 0;
        margin-left: 0;
        text-align: left;
    }

    .indicator-layanan-item {
        display: flex;
        align-items: baseline;
        gap: 4px;
        width: 100%;
        margin-left: 0;
        justify-content: flex-start;
    }

    .indicator-layanan-item {
        display: flex;
        align-items: baseline;
        gap: 4px;
        width: 100%;
    }

    .indicator-layanan-value {
        font-size: 56px;
        font-weight: 700;
        color: #1C2B78;
        line-height: 1;
        font-family: 'Poppins', sans-serif;
        padding-top: 4px;
    }

    .indicator-layanan-plus {
        font-size: 28px;
        font-weight: 700;
        color: #FFA84A;
        line-height: 1;
        margin-left: -4px;
        align-self: flex-end;
        margin-bottom: 0;
        padding-top: 4px;
    }

    .indicator-layanan-labels {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-left: 8px;
        justify-content: flex-end;
        align-items: flex-start;
        align-self: flex-end;
        margin-bottom: 0;
    }

    .indicator-layanan-label-small {
        font-size: 11px;
        font-weight: 400;
        color: #FFA84A;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0;
    }

    .indicator-layanan-label-large {
        font-size: 18px;
        font-weight: 700;
        color: #1C2B78;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 1024px) {
        .indicator-stats-top {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .indicator-stat-value {
            font-size: 42px;
        }

        .indicator-layanan-value {
            font-size: 42px;
        }

        .indicator-layanan-plus {
            font-size: 32px;
        }

        .indicator-stats-content {
            padding: 24px 20px;
            gap: 32px;
        }
    }

    @media (max-width: 640px) {
        .indicator-stats-top {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .indicator-stat-value {
            font-size: 48px;
        }

        .indicator-layanan-value {
            font-size: 48px;
        }

        .indicator-layanan-plus {
            font-size: 36px;
        }

        .indicator-stats-content {
            padding: 24px 16px;
            gap: 28px;
        }
    }
</style>
@endpush

@section('content')
{{-- Hero Section Slider --}}
<section id="hero-slider" class="hero-slider" style="width: 100vw; max-width: 100%; margin-top: 0; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
    @foreach($heroSlides as $index => $slide)
        @php
            // Ensure image path is properly formatted
            $imageUrl = $slide['gambar'] ?? asset('assets/img/hero_section.png');
            // Double check - if it's still a relative path, convert it
            if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://') && !str_starts_with($imageUrl, '/')) {
                // If it starts with hero/ or storage/, add asset()
                if (str_starts_with($imageUrl, 'hero/') || str_starts_with($imageUrl, 'storage/')) {
                    $imageUrl = asset($imageUrl);
                } elseif (str_starts_with($imageUrl, 'assets/')) {
                    $imageUrl = asset($imageUrl);
                }
            }
        @endphp
        <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $imageUrl }}');">
            <div class="absolute inset-0 hero-overlay"></div>
            <div class="hero-slide-content {{ $slide['alignment'] }}">
                <div class="w-full max-w-[1200px] mx-auto px-6 md:px-12 lg:px-24">
                    <div class="{{ $slide['alignment'] === 'center' ? 'max-w-3xl mx-auto' : ($slide['alignment'] === 'right' ? 'max-w-2xl ml-auto' : 'max-w-2xl') }}">
                        @if(!empty($slide['subjudul']))
                            <p class="text-lg md:text-xl text-white mb-4 drop-shadow-md">{{ $slide['subjudul'] }}</p>
                        @endif
                        
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-8 drop-shadow-lg leading-tight">{{ $slide['judul'] }}</h1>
                        
                        @if(!empty($slide['teks_button']) && !empty($slide['button_link']))
                            @php
                                $buttonClasses = $slide['button_style'] === 'navy-orange' 
                                    ? 'bg-[#102A66] hover:bg-[#FFA84A]' 
                                    : 'bg-[#FFA84A] hover:bg-[#102A66]';
                                
                                $onClickHandler = '';
                                if (str_starts_with($slide['button_link'], '#')) {
                                    $onClickHandler = "onclick=\"event.preventDefault(); document.getElementById('" . substr($slide['button_link'], 1) . "').scrollIntoView({ behavior: 'smooth', block: 'start' });\"";
                                }
                            @endphp
                            <a href="{{ $slide['button_link'] }}" 
                               {!! $onClickHandler !!}
                               class="{{ $buttonClasses }} inline-flex items-center gap-2 text-white font-semibold px-8 py-4 rounded-lg text-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                                <span>{{ $slide['teks_button'] }}</span>
                                @if($slide['button_icon'] !== 'none')
                                    @include('partials.hero-button-icon', ['icon' => $slide['button_icon']])
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Navigation Arrows --}}
    <button id="hero-prev-btn" class="hero-slider-nav prev">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>
    <button id="hero-next-btn" class="hero-slider-nav next">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>

    {{-- Dots Indicator --}}
    <div class="hero-slider-controls">
        @foreach($heroSlides as $index => $slide)
            <button class="hero-slider-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
</section>

{{-- Definisi dan Tujuan SPBE Section --}}
<section id="definisi" class="bg-[#FFFFFF] py-16 mb-12 relative overflow-x-hidden px-6 md:px-12 lg:px-24">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Left: Text Content dari Panel --}}
            <div>
                @if($section)
                <h2 class="text-4xl font-bold text-[#1c2b78] mb-6">{{ $section->judul }}</h2>
                <div class="text-gray-700 leading-relaxed text-lg prose prose-lg max-w-none">
                    {!! $section->deskripsi_html !!}
                </div>
                @else
                <h2 class="text-4xl font-bold text-[#1c2b78] mb-6">Definisi dan Tujuan SPBE</h2>
                <p class="text-gray-700 leading-relaxed text-lg">
                    Sistem Pemerintahan Berbasis Elektronik (SPBE) adalah sistem penyelenggaraan pemerintahan yang
                    memanfaatkan teknologi informasi dan komunikasi untuk meningkatkan kualitas layanan publik,
                    efisiensi, efektivitas, transparansi, dan akuntabilitas penyelenggaraan pemerintahan. SPBE dirancang
                    untuk mendukung terwujudnya tata kelola pemerintahan yang baik melalui pemanfaatan teknologi digital
                    secara optimal.
                </p>
                @endif
            </div>

            {{-- Right: Features Grid dari Panel --}}
            <div class="grid grid-cols-2 gap-6 self-start items-stretch" style="margin-top: 4.5rem;">
                @if($tujuan && $tujuan->count() > 0)
                @foreach($tujuan as $tujuanItem)
                <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow flex flex-col h-full">
                    @if($tujuanItem->ikon)
                    <div class="text-4xl mb-3 flex-shrink-0" style="min-height: 2.5rem;">{!! $tujuanItem->ikon !!}</div>
                    @else
                    <div class="mb-3 flex-shrink-0" style="min-height: 2.5rem;"></div>
                    @endif
                    <h3 class="text-lg font-bold text-[#1c2b78] mb-2 flex-shrink-0" style="min-height: 3.5rem; line-height: 1.75rem;">{{ $tujuanItem->judul }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $tujuanItem->deskripsi }}</p>
                </div>
                @endforeach
                @else
                {{-- Fallback jika belum ada data --}}
                @php
                $features = [
                [
                'icon' => '🔧',
                'title' => 'Tersedianya layanan digital',
                'description' => 'Menyediakan layanan publik yang dapat diakses secara digital untuk memudahkan
                masyarakat.'
                ],
                [
                'icon' => '📄',
                'title' => 'Efisiensi dan efektivitas',
                'description' => 'Meningkatkan efisiensi dan efektivitas proses pemerintahan melalui digitalisasi.'
                ],
                [
                'icon' => '⚙',
                'title' => 'Peningkatan kualitas',
                'description' => 'Meningkatkan kualitas layanan dan tata kelola pemerintahan secara berkelanjutan.'
                ],
                [
                'icon' => '🌐',
                'title' => 'Transparansi dan akuntabilitas',
                'description' => 'Meningkatkan transparansi dan akuntabilitas dalam penyelenggaraan pemerintahan.'
                ]
                ];
                @endphp

                @foreach($features as $feature)
                <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow flex flex-col h-full">
                    <div class="text-4xl mb-3 flex-shrink-0" style="min-height: 2.5rem;">{{ $feature['icon'] }}</div>
                    <h3 class="text-lg font-bold text-[#1c2b78] mb-2 flex-shrink-0" style="min-height: 3.5rem; line-height: 1.75rem;">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['description'] }}</p>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Progres SPBE --}}
<section class="dual-tone-section dual-tone-section--progress" x-data="{
    currentSlide: 0,
    itemsPerSlide: 4,
    totalItems: {{ count($progresSpbe ?? []) }},
    get totalSlides() {
        return Math.ceil(this.totalItems / this.itemsPerSlide);
    },
    get canGoPrev() {
        return this.currentSlide > 0;
    },
    get canGoNext() {
        return this.currentSlide < this.totalSlides - 1;
    },
    prevSlide() {
        if (this.canGoPrev) {
            this.currentSlide--;
        }
    },
    nextSlide() {
        if (this.canGoNext) {
            this.currentSlide++;
        }
    },
    init() {
        // Adjust items per slide based on screen size
        const updateItemsPerSlide = () => {
            if (window.innerWidth <= 640) {
                this.itemsPerSlide = 1;
            } else if (window.innerWidth <= 1280) {
                this.itemsPerSlide = 2;
            } else {
                this.itemsPerSlide = 4;
            }
        };
        updateItemsPerSlide();
        window.addEventListener('resize', updateItemsPerSlide);
    }
}">
    <div class="dual-tone-section__top">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="section-heading">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <span class="section-heading__eyebrow">Progres SPBE</span>
                    <span class="section-heading__title">Perkembangan Terkini</span>
                </div>
                <div class="progress-pagination" x-show="totalSlides > 1">
                    <button
                        class="progress-pagination-btn"
                        @click="prevSlide()"
                        :disabled="!canGoPrev"
                        aria-label="Previous slide">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button
                        class="progress-pagination-btn"
                        @click="nextSlide()"
                        :disabled="!canGoNext"
                        aria-label="Next slide">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="dual-tone-section__bottom">
        <div class="cards-floating">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(isset($progresSpbe) && count($progresSpbe) > 0)
                <div class="progress-slider-wrapper">
                    <div class="progress-slider-container" :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @php
                        // Pastikan kita menggunakan collection, bukan array
                        $progresCollection = is_object($progresSpbe) && method_exists($progresSpbe, 'chunk') ? $progresSpbe : collect($progresSpbe);
                        $chunks = $progresCollection->chunk(4);
                        @endphp
                        @foreach($chunks as $chunk)
                        <div class="progress-slide">
                            @foreach($chunk as $progres)
                            <a href="{{ route('informasi-spbe.show', 'progres-' . $progres->id) }}" class="progress-wrapper block">
                                <div class="progress-title-box">
                                    {{ $progres->judul }}
                                </div>
                                <div class="progress-card">
                                    <div class="progress-content-box">
                                        @if($progres->gambar && $progres->gambar_url)
                                        <img
                                            src="{{ $progres->gambar_url }}"
                                            alt="{{ $progres->judul }}"
                                            style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; margin: 0; padding: 0;"
                                            loading="lazy"
                                            decoding="async">
                                        @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <span class="text-gray-400 text-sm">No Image</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center text-gray-500 py-8">
                    <p>Belum ada data progres yang tersedia.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Informasi SPBE & Indikator --}}
<section class="dual-tone-section dual-tone-section--indikator">
    <div class="dual-tone-section__top">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="section-heading section-heading--stacked">
                <span class="section-heading__eyebrow">Data & Visualisasi</span>
                <span class="section-heading__title">Informasi SPBE & Indikator</span>
            </div>
        </div>
    </div>

    <div class="dual-tone-section__bottom">
        <div class="cards-floating">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Informasi SPBE dengan Chart --}}
                    <div class="progress-wrapper">
                        <div class="progress-title-box">
                            Informasi SPBE
                        </div>
                        <div class="progress-card">
                            <div class="progress-content-box">
                                <div id="spbeIndexChart" data-chart-data="{{ json_encode($chartData ?? []) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Indikator --}}
                    <div class="progress-wrapper">
                        <div class="progress-title-box">
                            INDIKATOR
                        </div>
                        <div class="progress-card">
                            <div class="progress-content-box">
                                <div class="indicator-stats">
                                    <div class="indicator-stats-content">
                                        @php
                                            // Pisahkan indikator berdasarkan kategori
                                            $indikatorTop = collect($indikatorSpbe ?? [])
                                                ->filter(fn($item) => in_array($item->kategori, ['Kebijakan SPBE', 'Tata Kelola SPBE', 'Manajemen SPBE']))
                                                ->sortBy(function($item) {
                                                    $order = ['Kebijakan SPBE' => 1, 'Tata Kelola SPBE' => 2, 'Manajemen SPBE' => 3];
                                                    return $order[$item->kategori] ?? 999;
                                                })
                                                ->values();
                                            
                                            $indikatorLayanan = collect($indikatorSpbe ?? [])
                                                ->firstWhere('kategori', 'Layanan SPBE');
                                        @endphp
                                        
                                        {{-- Top Section: 3 Stats --}}
                                        <div class="indicator-stats-top">
                                            @foreach($indikatorTop as $indikator)
                                                <div class="indicator-stat-item">
                                                    <div class="indicator-stat-number">
                                                        <span class="indicator-stat-value">{{ $indikator->nilai }}</span>
                                                        <span class="indicator-stat-square"></span>
                                                    </div>
                                                    <div class="indicator-stat-label">{{ $indikator->kategori }}</div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Bottom Section: Layanan SPBE --}}
                                        <div class="indicator-stats-bottom">
                                            <div class="indicator-layanan-heading">Layanan SPBE</div>
                                            <div class="indicator-layanan-item">
                                                <div class="indicator-stat-number">
                                                    <span class="indicator-layanan-value">{{ $indikatorLayanan->nilai ?? '16' }}</span>
                                                    <span class="indicator-layanan-plus">+</span>
                                                </div>
                                                <div class="indicator-layanan-labels">
                                                    <span class="indicator-layanan-label-small">INDIKATOR</span>
                                                    <span class="indicator-layanan-label-large">LAYANAN</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Informasi Seputar SPBE Kabupaten Malang --}}
<section class="dual-tone-section dual-tone-section--informasi">
    <div class="dual-tone-section__top">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="section-heading section-heading--stacked">
                <span class="section-heading__eyebrow">Berita Terkini</span>
                <span class="section-heading__title">Informasi Seputar SPBE Kabupaten Malang</span>
            </div>
        </div>
    </div>

    <div class="dual-tone-section__bottom">
        <div class="cards-floating">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @forelse($informasiSpbe as $item)
                    <div class="news-card">
                        <div class="news-image-wrapper">
                            @if($item['image_url'])
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="news-image" loading="lazy" decoding="async">
                            @else
                            <img src="https://via.placeholder.com/400x200?text=Informasi+SPBE" alt="Gambar Informasi"
                                class="news-image" loading="lazy" decoding="async">
                            @endif

                            @if($item['category'])
                            <span class="news-category-tag">{{ $item['category'] }}</span>
                            @endif
                        </div>
                        <div class="news-card-content">
                            <h3 class="news-title line-clamp-2">
                                <a href="{{ route('informasi-spbe.show', $item['slug']) }}">
                                    {{ $item['title'] }}
                                </a>
                            </h3>
                            <div class="news-meta">
                                <span class="news-date">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ optional($item['date'])->translatedFormat('d M Y') }}
                                </span>
                                <span class="news-meta-separator">|</span>
                                <a href="{{ route('informasi-spbe.show', $item['slug']) }}" class="news-read-article">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Baca Artikel
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="col-span-4 text-center text-slate-500">
                        Informasi SPBE belum dapat dimuat saat ini.
                    </p>
                    @endforelse
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('informasi-spbe.index') }}" class="btn-load-more">
                        Lihat Semua Informasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Berita Terkini --}}
<section class="dual-tone-section dual-tone-section--news">
    <div class="dual-tone-section__top">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="section-heading section-heading--stacked">
                <span class="section-heading__eyebrow">Berita Terkini</span>
                <span class="section-heading__title">Informasi Seputar Kabupaten Malang</span>
            </div>
        </div>
    </div>

    <div class="dual-tone-section__bottom">
        <div class="cards-floating">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @forelse($beritaKabupaten as $item)
                    <div class="news-card">
                        <div class="news-image-wrapper">
                            @if($item['image_url'])
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="news-image" loading="lazy" decoding="async">
                            @else
                            <img src="https://via.placeholder.com/400x200?text=Berita+SPBE" alt="Gambar Berita"
                                class="news-image" loading="lazy" decoding="async">
                            @endif

                            @if($item['category'])
                            <span class="news-category-tag">{{ $item['category'] }}</span>
                            @endif
                        </div>
                        <div class="news-card-content">
                            <h3 class="news-title line-clamp-2">
                                <a href="{{ route('berita-kab-malang.show', $item['slug']) }}">
                                    {{ $item['title'] }}
                                </a>
                            </h3>
                            <div class="news-meta">
                                <span class="news-date">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ optional($item['date'])->translatedFormat('d M Y') }}
                                </span>
                                <span class="news-meta-separator">|</span>
                                <a href="{{ route('berita-kab-malang.show', $item['slug']) }}" class="news-read-article">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Baca Artikel
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="col-span-4 text-center text-slate-500">
                        Berita Kabupaten Malang belum dapat dimuat saat ini.
                    </p>
                    @endforelse
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('berita-kab-malang.index') }}" class="btn-load-more">
                        Lihat Semua Berita
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<!-- Hero Slider JavaScript -->
<script>
    (function() {
        let currentSlide = 0;
        const slides = document.querySelectorAll('#hero-slider .hero-slide');
        const dots = document.querySelectorAll('#hero-slider .hero-slider-dot');
        const totalSlides = slides.length;

        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => {
                slide.classList.remove('active');
                // Reset content animation
                const content = slide.querySelector('.hero-slide-content');
                if (content) {
                    content.style.transition = 'none';
                    content.style.opacity = '0';
                    content.style.visibility = 'hidden';
                }
            });
            dots.forEach(dot => dot.classList.remove('active'));

            // Force reflow before adding active class
            void slides[0].offsetHeight;

            // Add active class to current slide and dot
            if (slides[index]) {
                const activeSlide = slides[index];
                const activeContent = activeSlide.querySelector('.hero-slide-content');
                
                // Add active class first
                activeSlide.classList.add('active');
                
                // Then restore transition and visibility
                if (activeContent) {
                    // Use requestAnimationFrame to ensure transition triggers
                    requestAnimationFrame(() => {
                        activeContent.style.transition = '';
                        activeContent.style.opacity = '';
                        activeContent.style.visibility = '';
                        // Force reflow to trigger transition
                        void activeContent.offsetHeight;
                    });
                }
            }
            if (dots[index]) {
                dots[index].classList.add('active');
            }

            currentSlide = index;
        }

        function nextSlide() {
            const next = (currentSlide + 1) % totalSlides;
            showSlide(next);
        }

        function prevSlide() {
            const prev = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(prev);
        }

        function goToSlide(index) {
            if (index >= 0 && index < totalSlides) {
                showSlide(index);
            }
        }

        // Event listeners
        const prevBtn = document.getElementById('hero-prev-btn');
        const nextBtn = document.getElementById('hero-next-btn');

        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlide);
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });

        // Auto-play slider (pause on hover)
        let autoPlayInterval = setInterval(nextSlide, 8000);
        const heroSlider = document.getElementById('hero-slider');

        if (heroSlider) {
            heroSlider.addEventListener('mouseenter', () => {
                clearInterval(autoPlayInterval);
            });

            heroSlider.addEventListener('mouseleave', () => {
                autoPlayInterval = setInterval(nextSlide, 8000);
            });
        }
    })();
</script>

<!-- ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data chart dari server
        const chartContainer = document.getElementById('spbeIndexChart');
        if (!chartContainer) return;

        const chartDataAttr = chartContainer.getAttribute('data-chart-data');
        if (!chartDataAttr) {
            chartContainer.innerHTML = '<p class="text-center text-gray-500 py-8">Data Indeks SPBE belum tersedia</p>';
            return;
        }

        const chartData = JSON.parse(chartDataAttr);

        if (chartData.length === 0) {
            // Jika tidak ada data, tampilkan pesan
            chartContainer.innerHTML = '<p class="text-center text-gray-500 py-8">Data Indeks SPBE belum tersedia</p>';
            return;
        }

        // Ambil dan urutkan semua tahun dari data (dinamis, bisa bertambah sewaktu-waktu)
        const years = chartData
            .map(item => item.year)
            .filter(year => year)
            .sort((a, b) => parseInt(a) - parseInt(b));

        const values = years.map(year => {
            const item = chartData.find(d => d.year === year);
            return item && item.value !== null && item.value !== undefined ? parseFloat(item.value) : null;
        });

        // Konfigurasi ApexCharts
        const options = {
            series: [{
                name: 'Indeks SPBE',
                data: values
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                offsetX: 15,
                offsetY: 0,
                parentHeightOffset: 0,
                fontFamily: 'Poppins, sans-serif'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#1C2B78']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.2,
                    stops: [0, 100],
                    colorStops: [{
                            offset: 0,
                            color: '#1C2B78',
                            opacity: 0.6
                        },
                        {
                            offset: 100,
                            color: '#1C2B78',
                            opacity: 0.2
                        }
                    ]
                }
            },
            markers: {
                size: 5,
                colors: ['#1C2B78'],
                strokeColors: '#ffffff',
                strokeWidth: 2,
                hover: {
                    size: 7,
                    colors: ['#FFA84A']
                }
            },
            xaxis: {
                categories: years,
                tickPlacement: 'on',
                labels: {
                    style: {
                        colors: '#FFA84A',
                        fontSize: '12px',
                        fontFamily: 'Poppins, sans-serif',
                        fontWeight: 600
                    },
                    rotate: -30,
                    rotateAlways: false,
                    trim: true
                },
                title: {
                    text: 'Tahun',
                    style: {
                        color: '#1C2B78',
                        fontSize: '14px',
                        fontWeight: 'bold',
                        fontFamily: 'Poppins, sans-serif'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                offsetX: 0,
                offsetY: 0
            },
            yaxis: {
                min: 0,
                max: 5,
                tickAmount: 5,
                labels: {
                    style: {
                        colors: '#FFA84A',
                        fontSize: '12px',
                        fontFamily: 'Poppins, sans-serif',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return val.toFixed(2);
                    },
                    offsetX: 0
                },
                title: {
                    text: 'Indeks',
                    style: {
                        color: '#1C2B78',
                        fontSize: '14px',
                        fontWeight: 'bold',
                        fontFamily: 'Poppins, sans-serif'
                    },
                    offsetX: -5
                }
            },
            grid: {
                show: false,
                padding: {
                    left: 40,
                    right: 70,
                    top: 0,
                    bottom: 0
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '13px',
                    fontFamily: 'Poppins, sans-serif'
                },
                marker: {
                    show: true,
                    fillColors: ['#FFA84A']
                },
                y: {
                    formatter: function(val) {
                        return 'Indeks: ' + val.toFixed(2);
                    }
                }
            },
            title: {
                text: 'Indeks SPBE',
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: '600',
                    color: '#FFA84A',
                    fontFamily: 'Poppins, sans-serif'
                },
                offsetY: 10
            },
            subtitle: {
                text: 'Pemerintah Kabupaten Malang',
                align: 'center',
                style: {
                    fontSize: '14px',
                    fontWeight: '400',
                    color: '#102A66',
                    fontFamily: 'Poppins, sans-serif'
                },
                offsetY: 35
            },
            plotOptions: {
                area: {
                    fillTo: 'end'
                }
            }
        };

        const chart = new ApexCharts(chartContainer, options);
        chart.render();
    });
</script>
@endpush

@endsection