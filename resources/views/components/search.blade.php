<style>
    .search-bar {
        width: 100%;
        display: flex;
        align-items: center;
        position: relative;
    }

    #search-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 10px;
    }

    #search-button {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
    }

    #search-button:hover {
        background-color: #0056b3;
    }

    #search-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
    }

    #search-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #search-dropdown li {
        padding: 10px;
        cursor: pointer;
    }

    #search-dropdown li:hover {
        background-color: #f0f0f0;
    }
</style>

<div class="search-bar">
    <form action="{{ route('search') }}" method="GET">
        @csrf
        <input type="text" id="search-input" placeholder="Tìm kiếm...">
        <button  type="submit" id="search-button">Tìm kiếm</button>
    </form>
</div>

