<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="110"
        height="85"
        viewBox="0 0 110 85"
    >
        <defs>
            <filter
                id="yvvsl7voia"
                width="126.1%"
                height="136%"
                x="-13%"
                y="-18%"
                filterUnits="objectBoundingBox"
            >
                <feOffset dy="2" in="SourceAlpha" result="shadowOffsetOuter1" />
                <feGaussianBlur
                    in="shadowOffsetOuter1"
                    result="shadowBlurOuter1"
                    stdDeviation="2"
                />
                <feColorMatrix
                    in="shadowBlurOuter1"
                    result="shadowMatrixOuter1"
                    values="0 0 0 0 0.446105072 0 0 0 0 0.439058176 0 0 0 0 0.439058176 0 0 0 0.5 0"
                />
                <feMerge>
                    <feMergeNode in="shadowMatrixOuter1" />
                    <feMergeNode in="SourceGraphic" />
                </feMerge>
            </filter>
        </defs>
        <g fill="none" fill-rule="evenodd">
            <g>
                <g>
                    <g
                        filter="url(#yvvsl7voia)"
                        transform="translate(-542 -247) translate(495 232) translate(56 24)"
                    >
                        <g>
                            <g>
                                <rect
                                    width="60"
                                    height="48.621"
                                    fill="#FFF"
                                    rx="5"
                                />
                                <path
                                    fill="#F2B737"
                                    d="M7.241 15.517H53.793V34.138H7.241z"
                                />
                                <rect
                                    width="60"
                                    height="48.621"
                                    x="32"
                                    y="18"
                                    fill="#FFF"
                                    rx="5"
                                />
                            </g>
                            <g fill="#F2B737">
                                <path
                                    d="M0 0H49V19H0z"
                                    transform="matrix(1 0 0 -1 38 50)"
                                />
                            </g>
                        </g>
                    </g>
                </g>
            </g>
        </g>
    </svg>
</template>
