<style>
    .total-summary {
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #363B64;
        line-height: 1;
        gap: 8px;

        &:hover {
            background-color: #AA0000;
            color: #fff;
        }

        i {
            font-size: 40px;
        }

        .label {
            font-size: 16px;
            font-weight: 600;
        }

        .total {
            font-size: 32px;
            font-weight: 600;
        }
    }

    @media (min-width: 576px) {
        .total-summary {
            i {
                font-size: 50px;
            }

            .label {
                font-size: 18px;
            }

            .total {
                font-size: 40px;
            }
        }
    }

    /* @media (min-width: 768px) {
        .total-summary {
            i {
                font-size: 60px;
            }

            .label {
                font-size: 16px;
            }

            .total {
                font-size: 32px;
            }
        }
    } */

    @media (min-width: 1200px) {
        .total-summary {
            i {
                font-size: 84px;
            }

            .label {
                font-size: 20px;
            }

            .total {
                font-size: 40px;
            }
        }
    }
</style>

@props(['icon' => '', 'label' => '', 'total' => ''])

<x-landing.card class="ratio-1 total-summary">
    <i class="isax isax-{{ $icon }}"></i>
    <span class="label">{{ $label }}</span>
    <span class="total">{{ $total }}</span>
</x-landing.card>
