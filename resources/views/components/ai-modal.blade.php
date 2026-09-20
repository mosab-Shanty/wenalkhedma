<div x-show="aiOpen" 
     x-cloak 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
     x-data="aiAssistantModal()">

    <div @click.away="aiOpen = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-5 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-wand-magic-sparkles text-xl text-yellow-300"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg">المساعد الذكي (وين الخدمة)</h3>
                    <p class="text-xs text-emerald-100 font-medium">اسأل عن أقرب الخدمات أو حالتها الفورية</p>
                </div>
            </div>
            <button @click="aiOpen = false" class="text-white/80 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 space-y-4 overflow-y-auto flex-grow">
            
            <!-- Default Prompt Suggestions -->
            <div x-show="!responseHtml && !loading" class="space-y-3">
                <p class="text-xs font-bold text-slate-500">جرب البحث بكلمات سريعة:</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="submitPrompt('اعرضلي أقرب صيدلية متاحة الآن')" class="bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-xs font-bold text-slate-700 px-3 py-2 rounded-xl transition-all text-right">
                        💊 أقرب صيدلية متاحة الآن
                    </button>
                    <button type="button" @click="submitPrompt('وين أقرب مخبز؟')" class="bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-xs font-bold text-slate-700 px-3 py-2 rounded-xl transition-all text-right">
                        🥖 وين أقرب مخبز؟
                    </button>
                    <button type="button" @click="submitPrompt('نقاط توزيع مياه شرب')" class="bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-xs font-bold text-slate-700 px-3 py-2 rounded-xl transition-all text-right">
                        💧 نقاط توزيع مياه شرب
                    </button>
                    <button type="button" @click="submitPrompt('شحن كهرباء طاقة شمسية')" class="bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-xs font-bold text-slate-700 px-3 py-2 rounded-xl transition-all text-right">
                        ⚡ نقاط شحن كهرباء
                    </button>
                </div>
            </div>

            <!-- Reset / New Search Button -->
            <div x-show="responseHtml && !loading" class="flex justify-end">
                <button type="button" @click="responseHtml = ''; prompt = '';" class="text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-rotate-right"></i> بحث جديد
                </button>
            </div>

            <!-- Loading Spinner -->
            <div x-show="loading" class="py-12 text-center space-y-3">
                <div class="inline-block animate-spin text-emerald-600 text-3xl">
                    <i class="fa-solid fa-circle-notch"></i>
                </div>
                <p class="text-xs font-bold text-slate-600">جاري تحليل الطلب واستخراج أفضل الخدمات...</p>
            </div>

            <!-- Dynamic Result HTML -->
            <div x-show="responseHtml" x-html="responseHtml"></div>

        </div>

        <!-- Input Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center gap-2">
            <input type="text" 
                   x-model="prompt" 
                   @keydown.enter.prevent="submitPrompt()" 
                   placeholder="اكتب مثل: أقرب صيدلية متاحة في غزة..." 
                   class="flex-grow bg-white border border-slate-200 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
            
            <button type="button" 
                    @click="submitPrompt()" 
                    :disabled="loading || !prompt.trim()"
                    class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all flex items-center gap-1 shadow-md shadow-emerald-600/20">
                <span>بحث</span>
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </div>

    </div>

</div>

<script>
    function aiAssistantModal() {
        return {
            prompt: '',
            loading: false,
            responseHtml: '',
            submitPrompt(text) {
                if (text) {
                    this.prompt = text;
                }
                const queryText = this.prompt.trim();
                if (!queryText) return;

                this.loading = true;
                this.responseHtml = '';

                fetch('{{ route('ai.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: queryText })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data && data.success) {
                        this.responseHtml = data.html;
                    } else {
                        this.responseHtml = '<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-2xl text-center">عذراً، لم نتمكن من معالجة هذا البحث حالياً.</div>';
                    }
                })
                .catch(err => {
                    console.error('AI Assistant Error:', err);
                    this.loading = false;
                    this.responseHtml = '<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-2xl text-center">حدث خطأ في الاتصال بالخادم، يرجى إعادة المحاولة.</div>';
                });
            }
        };
    }
</script>
