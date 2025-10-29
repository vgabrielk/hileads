<!-- Plans List Items -->
<div class="divide-y divide-border">
    @php
        $colors = ['green', 'orange', 'yellow', 'purple', 'blue', 'pink'];
    @endphp
    @foreach($plans as $index => $plan)
        @php
            $color = $colors[$index % count($colors)];
        @endphp
        <div class="flex items-center gap-4 px-6 py-5 hover:bg-accent/30 transition-all duration-200 group relative cursor-pointer" 
             @if(!auth()->user()->isAdmin()) onclick="startCheckout({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})" @endif>
            <!-- Status Indicator Bar -->
            <div class="w-1 h-20 rounded-full flex-shrink-0 bg-{{ $color }}-500"></div>
            
            <!-- Plan Info -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-base font-semibold text-foreground group-hover:text-primary transition-colors">
                                {{ $plan->name }}
                            </h3>
                            @if($plan->is_popular)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary text-primary-foreground">
                                    Popular
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-muted-foreground mb-2">{{ $plan->description }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 text-sm flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-foreground">{{ $plan->formatted_price }}</span>
                        <span class="text-muted-foreground">{{ $plan->interval_description }}</span>
                    </div>
                    @if($plan->max_contacts)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-muted text-muted-foreground">
                            {{ number_format($plan->max_contacts) }} contatos
                        </span>
                    @endif
                    @if($plan->max_campaigns)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-muted text-muted-foreground">
                            {{ $plan->max_campaigns }} campanhas
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Arrow Icon / Admin Badge -->
            <div class="flex-shrink-0">
                @if(auth()->user()->isAdmin())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                        Admin
                    </span>
                @else
                    <svg class="w-5 h-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            </div>
        </div>
    @endforeach
</div>

