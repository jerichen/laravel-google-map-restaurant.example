<div class="panel-body" id='panelContent'></div>

<div class="panel-body" id='specialContent' style="display: none;">
    <div class="form-group" id="specificFoodBody">
        <label for="searchFood">您想搜尋其他嘛?</label>
        <div class="input-group">
            <input type="text" class="form-control" id="searchFood" placeholder="填入關鍵字" aria-describedby="sizing-addon2" required />
        </div>
        <br>
        <button type="submit" class="btn btn-default" onclick="specialSearch(this)">Search</button>
        <button type="submit" class="btn btn-warning" onclick="specialClose(this)">關閉</button>
    </div>
    <div id="searchFoodResult"></div>
</div>
