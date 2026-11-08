@extends('layouts.dashboard')

@section('content')
    <div class="content">
        <h2 class="content-heading">
            <i class="fa-solid fa-book-open text-primary me-2"></i>Biblioteca de Contenidos
        </h2>
        <p class="text-muted mb-4">Material corporativo disponible para ti. Puedes consultarlo cuando lo necesites.</p>

        @if($categories->isEmpty())
            <div class="block block-rounded">
                <div class="block-content block-content-full text-center py-5 text-muted">
                    <i class="fa fa-folder-open fa-3x mb-3 d-block mx-auto"></i>
                    <h5 class="mb-1">No hay contenidos disponibles</h5>
                    <p class="mb-0">Por el momento no tienes material corporativo asignado.</p>
                </div>
            </div>
        @else
            <div class="accordion" id="libraryAccordion">
                @foreach($categories as $category)
                    @php $collapseId = 'category-' . $category->id; @endphp
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="heading-{{ $collapseId }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}">
                                <i class="fa-solid fa-layer-group text-primary me-2"></i>
                                {{ $category->name }}
                                <span class="badge bg-primary ms-2">{{ $category->subcategories->sum(fn($s) => $s->contents->count()) }}</span>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $collapseId }}" data-bs-parent="#libraryAccordion">
                            <div class="accordion-body">
                                @if($category->description)
                                    <p class="text-muted fs-sm">{{ $category->description }}</p>
                                @endif

                                @foreach($category->subcategories as $subcategory)
                                    <h5 class="fw-semibold mt-3 mb-3">
                                        <i class="fa-regular fa-folder-open text-warning me-1"></i>{{ $subcategory->name }}
                                    </h5>
                                    @if($subcategory->description)
                                        <p class="text-muted fs-sm mt-n2">{{ $subcategory->description }}</p>
                                    @endif

                                    <div class="row g-3">
                                        @foreach($subcategory->contents as $content)
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card h-100 border">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="mb-2">
                                                            @if($content->is_external)
                                                                <span class="badge bg-warning text-dark"><i class="fa fa-link me-1"></i>Enlace</span>
                                                            @elseif($content->type === 'pdf')
                                                                <span class="badge bg-danger"><i class="fa fa-file-pdf me-1"></i>PDF</span>
                                                            @else
                                                                <span class="badge bg-info"><i class="fa fa-video me-1"></i>Video</span>
                                                            @endif

                                                            @if($content->is_mandatory)
                                                                <span class="badge bg-primary">Obligatorio</span>
                                                            @endif
                                                        </div>
                                                        <h6 class="card-title fw-semibold mb-3">{{ $content->title }}</h6>

                                                        <div class="mt-auto">
                                                            @if($content->is_external)
                                                                <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
                                                                    <i class="fa fa-up-right-from-square me-1"></i> Abrir enlace
                                                                </a>
                                                            @else
                                                                <a href="{{ route('dashboard.corporate-contents.view', $content->id) }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                                    <i class="fa {{ $content->type === 'pdf' ? 'fa-file-pdf' : 'fa-circle-play' }} me-1"></i>
                                                                    {{ $content->type === 'pdf' ? 'Abrir documento' : 'Ver video' }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection