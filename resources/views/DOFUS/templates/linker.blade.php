<div class="ak-encyclo-detail ak-tooltip-linker ak-tooltip-linker-item">
   <div class="ak-top">
      <div class="ak-encyclo-detail-illu ak-illu">
         <img src="{{DofusForge::item($item->Template->IconId, 200)}}" />      
      </div>
      <div class="ak-detail">
         <div class="ak-name">{{$item->Template->Name}}</div>
         <div class="ak-type">{{$item->Template->Type->Name}}</div>
         <div class="ak-level">Niveau {{$item->Template->Level}}</div>
      </div>
   </div>
   @if(!empty($newEffectArray))
   <div class="ak-encyclo-detail">
      <div class="ak-container ak-tabs-container">
         <ul class="ak-container nav nav-tabs ak-tabs">
            <li class="tab1 active" >
               <a href="#tab1" >
               Effets    </a>
            </li>
         </ul>
         <div class="ak-container ak-tabs-body tab-content">
            <div id="tab1" class="ak-tab">
               <div class="ak-content-list ak-tabs-inner-content ak-displaymode-col">
                @foreach($newEffectArray as $k => $v)
                  <div class="ak-localepage @if($k > 0) hide @endif" data-page="{{$k + 1}}">
                  @foreach($v as $effect)
                     <div class="ak-list-element">
                        <div class="ak-main">
                           <div class="ak-main-content ">
                              <div class="ak-content">
                                 <div class="ak-title">
                                    {{$effect['text']}}                            
                                 </div>
                              </div>
                              @if($effect['asset'])
                              <div class="ak-aside">
                                 <span class="ak-tooltip ak-icon-small ak-{{$effect['asset']}}"></span><script type="application/json">{"manual":true,"tooltip":{"content":{"title":"","text":"{{$effect['name']}}"},"style":{"classes":"ak-tooltip-content"}},"forceOnTouch":true}</script>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  @endforeach
                  </div>
                @endforeach
                @if(count($newEffectArray) > 1)
                    <div class="text-center ak-pagination hidden-xs">
                        <nav>
                            <ul class="ak-pagination pagination">
                                <li class="disabled"><a href="javascript:void(0);" data-action="prev">« Précédent</a></li>
                                <li class=""><a href="" data-action="next">Suivant »</a></li>
                            </ul>
                            <script type="application/json">{"scroll":true}</script>
                        </nav>
                    </div>
                @endif
               </div>
            </div>
         </div>
      </div>
   </div>
   @endif
</div>