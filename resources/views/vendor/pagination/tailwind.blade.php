@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-top: 1.5rem;">
        
        {{-- Info Text --}}
        <p style="font-size: 0.8rem; color: #8E9BAE; font-weight: 600;">
            Menampilkan
            @if ($paginator->firstItem())
                <span style="font-weight: 700; color: #2D3E50;">{{ $paginator->firstItem() }}</span>
                sampai
                <span style="font-weight: 700; color: #2D3E50;">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            dari
            <span style="font-weight: 700; color: #2D3E50;">{{ $paginator->total() }}</span>
            data
        </p>

        {{-- Pagination Buttons --}}
        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.35rem; background: #F1F5F9; border-radius: 14px;">
            
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: transparent; color: #CBD5E1; cursor: not-allowed;">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: white; color: #2D3E50; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; color: #8E9BAE; font-weight: 700; font-size: 0.85rem;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: #0088A8; color: white; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(0, 136, 168, 0.25);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: white; color: #2D3E50; font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: white; color: #2D3E50; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: transparent; color: #CBD5E1; cursor: not-allowed;">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
