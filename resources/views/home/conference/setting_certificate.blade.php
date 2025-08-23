@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
<h2>Certificate Template Editor</h2>

<form method="POST" action="{{ $routeStore }}" enctype="multipart/form-data">
  @csrf

  <div class="mb-3">
    <label for="template" class="form-label fw-bold">Certificate Template</label>
    <input type="file" class="form-control @error('template') is-invalid @enderror" id="template"
      name="template" accept="image/*">
    <div class="form-text">Unggah gambar poster (JPG, PNG, GIF, dll.). Maksimal 2MB.</div>
    @error('template')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  @if($background != null)
  <div id="canvas-wrapper" style="position: relative; display: inline-block; border:1px solid #ccc;">
    <canvas id="certificateCanvas" width="842" height="595" style="cursor:move;"></canvas>
  </div>

  <div class="control-panel" style="margin-top: 20px;">
    <button type="button" class="btn btn-sm btn-primary" onclick="addText('name','{{ $data['name'] ?? 'Name' }}')">Add Name</button>
    {{-- <button type="button" class="btn btn-sm btn-primary" onclick="addText('conference','{{$data['conference'] ?? 'Conference'}}')">Add Conference</button>
    <button type="button" class="btn btn-sm btn-primary" onclick="addText('date','{{$data['date'] ?? 'Date'}}')">Add Date</button> --}}
    <button type="button" class="btn btn-sm btn-danger" onclick="removeSelectedText()">Remove Selected</button>
    <br><br>
    Selected Field: <span id="selectedField">None</span><br>
    Font Size: <input type="number" id="fontSize" value="24" min="8" max="100" style="width:60px;"> px<br>
    Width: <input type="number" id="textWidth" value="400" min="50" max="800" style="width:80px;"> px<br>
    Color: <input type="color" id="textColor" value="#000000"><br>
    Text Align: 
    <select id="textAlign">
      <option value="center">Center</option>
      <option value="left">Left</option>
      <option value="right">Right</option>
    </select>
  </div>

  <!-- Hidden input untuk menyimpan posisi -->
  <input type="hidden" name="positions" id="positionsInput">
  @endif

  <div style="margin-top:20px;">
    <button class="btn btn-primary" type="submit">
      @if($background != null)
        Save Template & Positions
      @else
        Upload Template
      @endif
    </button>
  </div>
</form>
@stop

@section('js')
<script>
const canvas = document.getElementById("certificateCanvas");
const ctx = canvas.getContext("2d");

let background = new Image();
background.src = @json($background); 

let texts = [];
let dragTarget = null;
let offsetX, offsetY;
let selected = null;

// Load saved positions dari DB
let savedPositions = @json($positions ?? '{}');
for(const field in savedPositions){
    const t = savedPositions[field];
    texts.push({
        type: field,
        text: t.text || field,
        x: parseFloat(t.x) || canvas.width/2,
        y: parseFloat(t.y) || canvas.height/2,
        size: parseInt(t.size) || 24,
        width: parseInt(t.width) || 500,
        color: t.color || '#000000',
        align: t.align || 'center'
    });
}

background.onload = () => {
    redraw();
    if(texts.length > 0) selectText(texts[0]);
};

// Tambah teks baru
function addText(type, text){
    // Cek jika field sudah ada
    const existingIndex = texts.findIndex(t => t.type === type);
    if(existingIndex >= 0){
        selectText(texts[existingIndex]);
        return;
    }

    const t = {
        type: type,
        text: text.toUpperCase(),
        x: canvas.width/2,
        y: canvas.height/2,
        size: parseInt(document.getElementById('fontSize').value) || 24,
        width: parseInt(document.getElementById('textWidth').value) || 400,
        color: document.getElementById('textColor').value || '#000000',
        align: document.getElementById('textAlign').value || 'center'
    };
    texts.push(t);
    selectText(t);
    redraw();
}

// Remove selected text
function removeSelectedText(){
    if(selected){
        const index = texts.findIndex(t => t === selected);
        if(index >= 0){
            texts.splice(index, 1);
            selected = null;
            document.getElementById('selectedField').innerText = 'None';
            redraw();
        }
    }
}

// Pilih teks
function selectText(t){
    selected = t;
    document.getElementById('selectedField').innerText = t.type;
    document.getElementById('fontSize').value = t.size;
    document.getElementById('textWidth').value = t.width;
    document.getElementById('textColor').value = t.color;
    document.getElementById('textAlign').value = t.align;
}

// Update properties
document.getElementById('fontSize').addEventListener('input', function(){
    if(selected){
        selected.size = parseInt(this.value);
        redraw();
    }
});

document.getElementById('textWidth').addEventListener('input', function(){
    if(selected){
        selected.width = parseInt(this.value);
        redraw();
    }
});

document.getElementById('textColor').addEventListener('input', function(){
    if(selected){
        selected.color = this.value;
        redraw();
    }
});

document.getElementById('textAlign').addEventListener('change', function(){
    if(selected){
        selected.align = this.value;
        redraw();
    }
});

// Gambar ulang canvas
function redraw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.drawImage(background,0,0,canvas.width,canvas.height);
    
    texts.forEach(t=>{
        ctx.font = `${t.size}px Arial`;
        ctx.fillStyle = t.color;
        ctx.textAlign = t.align;
        
        // Highlight selected text
        if(t === selected){
            ctx.strokeStyle = '#007bff';
            ctx.lineWidth = 2;
            const textHeight = t.size * 1.2;
            const lines = getTextLines(t.text, t.width, t.size);
            const totalHeight = lines.length * textHeight;
            
            ctx.strokeRect(
                t.x - t.width/2 - 5, 
                t.y - t.size - 5, 
                t.width + 10, 
                totalHeight + 10
            );
        }
        
        wrapText(ctx, t.text, t.x, t.y, t.width, t.size*1.2, t.align);
    });
    
    // Update hidden input dengan koordinat yang tepat
    const result = {};
    texts.forEach(t=>{
        result[t.type] = { 
            x: t.x, 
            y: t.y, 
            size: t.size, 
            width: t.width, 
            color: t.color,
            align: t.align,
            text: t.text 
        };
    });

    console.log(texts);
    document.getElementById("positionsInput").value = JSON.stringify(result);
}

// Fungsi untuk mendapatkan lines text
function getTextLines(text, maxWidth, fontSize){
    ctx.font = `${fontSize}px Arial`;
    const words = text.split(' ');
    let lines = [];
    let currentLine = '';
    
    for(let i = 0; i < words.length; i++){
        const testLine = currentLine + words[i] + ' ';
        const metrics = ctx.measureText(testLine);
        
        if(metrics.width > maxWidth && i > 0){
            lines.push(currentLine.trim());
            currentLine = words[i] + ' ';
        } else {
            currentLine = testLine;
        }
    }
    lines.push(currentLine.trim());
    return lines.filter(line => line.length > 0);
}

// Fungsi wrap text yang diperbaiki
function wrapText(context, text, x, y, maxWidth, lineHeight, align){
    const lines = getTextLines(text, maxWidth, context.font.split('px')[0]);
    
    lines.forEach((line, i) => {
        let textX = x;
        if(align === 'left') textX = x - maxWidth/2;
        else if(align === 'right') textX = x + maxWidth/2;
        else textX = x; // center
        
        context.fillText(line, textX, y + i * lineHeight);
    });
}

// Cari teks yang diklik - diperbaiki
function getClickedText(x, y){
    return texts.find(t => {
        const textHeight = t.size * 1.2;
        const lines = getTextLines(t.text, t.width, t.size);
        const totalHeight = lines.length * textHeight;
        
        return x >= t.x - t.width/2 && 
               x <= t.x + t.width/2 && 
               y >= t.y - t.size && 
               y <= t.y + totalHeight - t.size;
    });
}

// Drag events
canvas.addEventListener('mousedown', e=>{
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    dragTarget = getClickedText(mouseX, mouseY);
    if(dragTarget){
        selectText(dragTarget);
        offsetX = mouseX - dragTarget.x;
        offsetY = mouseY - dragTarget.y;
        redraw();
    }
});

canvas.addEventListener('mousemove', e=>{
    if(dragTarget){
        const rect = canvas.getBoundingClientRect();
        dragTarget.x = e.clientX - rect.left - offsetX;
        dragTarget.y = e.clientY - rect.top - offsetY;
        
        // Boundary check
        if(dragTarget.x < 0) dragTarget.x = 0;
        if(dragTarget.x > canvas.width) dragTarget.x = canvas.width;
        if(dragTarget.y < dragTarget.size) dragTarget.y = dragTarget.size;
        if(dragTarget.y > canvas.height) dragTarget.y = canvas.height;
        
        redraw();
    }
});

canvas.addEventListener('mouseup', e=>{
    dragTarget = null;
});

// Initialize
if(background.complete) {
    redraw();
    if(texts.length > 0) selectText(texts[0]);
}
</script>
@stop