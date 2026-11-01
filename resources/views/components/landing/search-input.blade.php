<style>
    .search-input-container {
        display: flex;
        align-items: center;
        position: relative;

        .icon {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            color: $main-color;

            &.prefix {
                left: 10px;
            }

            &.suffix {
                right: 10px;
            }
        }

        .search-input {
            padding: 8px 10px;
            padding-left: 40px;
            padding-right: 40px;
            border: none;
            width: 100%;
            box-sizing: border-box;
            outline: none;
            font-size: 14px;

            &:focus {
                border-color: #007bff;
            }
        }
    }
</style>

<div {{ $attributes->merge(['class' => 'search-input-container']) }}>
    <div class="icon prefix">
        <i class="isax-bold isax-search-normal-1"></i>
    </div>
    <input
        type="text"
        placeholder="Search here..."
        class="search-input"
    >
    <div class="icon suffix">
        <i class="isax-bold isax-camera"></i>
    </div>
</div>
