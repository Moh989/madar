<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Sector::firstOrCreate(['slug' => 'air-freight'], ['title' => ['ar' => 'الشحن الجوي', 'en' => 'Air freight'], 'excerpt' => ['ar' => 'مسارات جوية تتجاوز المسافات.', 'en' => 'Air connections beyond distance.'], 'body' => ['ar' => 'يمثّل الشحن الجوي أحد مجالات عمل مدار العالم. نرحب باستفساراتكم حول احتياجات نقل البضائع جواً، ومناقشة طبيعة الشحنة والمنشأ والوجهة والمتطلبات المرتبطة بها.', 'en' => 'Air freight is one of the company’s business areas. We welcome inquiries about moving goods by air, including the nature of the shipment, its origin, destination and associated requirements.'], 'services' => ['ar' => 'احتياجات الشحن الجوي
معلومات المنشأ والوجهة
طبيعة البضائع ومتطلبات النقل', 'en' => 'Air freight requirements
Origin and destination details
Cargo and transport requirements'], 'image' => 'images/air.webp', 'alt' => ['ar' => 'الشحن الجوي', 'en' => 'Air freight'], 'status' => 'published', 'sort_order' => 0]);
        Sector::firstOrCreate(['slug' => 'sea-freight'], ['title' => ['ar' => 'الشحن البحري', 'en' => 'Sea freight'], 'excerpt' => ['ar' => 'آفاق أوسع لحركة أعمالك.', 'en' => 'Wider horizons for your business.'], 'body' => ['ar' => 'الشحن البحري من الأنشطة الأساسية للشركة. شاركنا تفاصيل البضائع ومسار الشحن المطلوب لبدء مناقشة احتياجاتك البحرية.', 'en' => 'Sea freight is a core area of the company’s business. Share details of your goods and intended shipping route to begin discussing your maritime requirements.'], 'services' => ['ar' => 'متطلبات الشحن البحري
بيانات البضائع
المنشأ والوجهة', 'en' => 'Sea freight requirements
Cargo information
Origin and destination'], 'image' => 'images/sea.webp', 'alt' => ['ar' => 'الشحن البحري', 'en' => 'Sea freight'], 'status' => 'published', 'sort_order' => 1]);
        Sector::firstOrCreate(['slug' => 'contracting'], ['title' => ['ar' => 'المقاولات العامة', 'en' => 'General contracting'], 'excerpt' => ['ar' => 'نحو آفاق للبناء والتطوير.', 'en' => 'New horizons for development.'], 'body' => ['ar' => 'تشمل مجالات عمل الشركة المقاولات العامة. نرحب بمناقشة متطلبات المشاريع ونطاق الأعمال والوثائق الأولية لتحديد مجالات التعاون الممكنة.', 'en' => 'General contracting is part of the company’s business portfolio. We welcome discussions about project requirements, scope and initial documentation to identify potential collaboration.'], 'services' => ['ar' => 'متطلبات المشاريع
نطاق الأعمال
فرص التعاون', 'en' => 'Project requirements
Scope of works
Collaboration opportunities'], 'image' => 'images/industry.webp', 'alt' => ['ar' => 'المقاولات العامة', 'en' => 'General contracting'], 'status' => 'published', 'sort_order' => 2]);
        Sector::firstOrCreate(['slug' => 'real-estate'], ['title' => ['ar' => 'الاستثمارات العقارية', 'en' => 'Real estate investment'], 'excerpt' => ['ar' => 'رؤية للمكان، وآفاق للقيمة.', 'en' => 'A perspective on place and value.'], 'body' => ['ar' => 'تندرج الاستثمارات العقارية ضمن أنشطة مدار العالم. يمكنكم التواصل لمناقشة الفرص والمقترحات الاستثمارية والمعلومات الأولية المرتبطة بها.', 'en' => 'Real estate investment is among the company’s activities. Contact us to discuss opportunities, investment proposals and relevant preliminary information.'], 'services' => ['ar' => 'مقترحات الاستثمار العقاري
فرص التعاون
مناقشة معلومات المشاريع', 'en' => 'Real estate proposals
Collaboration opportunities
Project information'], 'image' => 'images/industry.webp', 'alt' => ['ar' => 'الاستثمارات العقارية', 'en' => 'Real estate investment'], 'status' => 'published', 'sort_order' => 3]);
        Sector::firstOrCreate(['slug' => 'industrial-investment'], ['title' => ['ar' => 'الاستثمارات الصناعية', 'en' => 'Industrial investment'], 'excerpt' => ['ar' => 'نفتح الحوار حول فرص الصناعة.', 'en' => 'Opening conversations on industry.'], 'body' => ['ar' => 'تمارس الشركة أنشطتها ضمن مجال الاستثمارات الصناعية. نستقبل الاستفسارات والمقترحات ذات الصلة بالمشاريع الصناعية واحتياجات تطويرها.', 'en' => 'Industrial investment is part of the company’s business scope. We welcome inquiries and proposals relating to industrial projects and their development requirements.'], 'services' => ['ar' => 'فرص الاستثمار الصناعي
متطلبات المشاريع
مقترحات التعاون', 'en' => 'Industrial investment opportunities
Project requirements
Collaboration proposals'], 'image' => 'images/industry.webp', 'alt' => ['ar' => 'الاستثمارات الصناعية', 'en' => 'Industrial investment'], 'status' => 'published', 'sort_order' => 4]);
        Sector::firstOrCreate(['slug' => 'supply'], ['title' => ['ar' => 'التجهيز', 'en' => 'Supply'], 'excerpt' => ['ar' => 'تبدأ متطلباتك بتفاصيل واضحة.', 'en' => 'Clear requirements start the conversation.'], 'body' => ['ar' => 'التجهيز أحد مجالات عمل الشركة. أرسل احتياجاتك والمواصفات والكميات المطلوبة لمناقشة طلب التجهيز بصورة واضحة.', 'en' => 'Supply is one of the company’s business areas. Send your requirements, specifications and quantities to discuss your supply request.'], 'services' => ['ar' => 'طلبات التجهيز
المواصفات والكميات
احتياجات الأعمال', 'en' => 'Supply requests
Specifications and quantities
Business requirements'], 'image' => 'images/industry.webp', 'alt' => ['ar' => 'التجهيز', 'en' => 'Supply'], 'status' => 'published', 'sort_order' => 5]);
        Sector::firstOrCreate(['slug' => 'agriculture'], ['title' => ['ar' => 'الإنتاج الزراعي والحيواني', 'en' => 'Agriculture & livestock production'], 'excerpt' => ['ar' => 'من الأرض، إلى فرص النمو.', 'en' => 'From the land to new possibilities.'], 'body' => ['ar' => 'يشمل نطاق أعمال مدار العالم الإنتاج الزراعي والحيواني. نرحب بطلبات التواصل المتعلقة بمشاريع الإنتاج واحتياجات الأعمال المرتبطة بهذا المجال.', 'en' => 'The company’s business scope includes agricultural and livestock production. We welcome inquiries about production projects and related business needs.'], 'services' => ['ar' => 'الإنتاج الزراعي
الإنتاج الحيواني
مقترحات التعاون', 'en' => 'Agricultural production
Livestock production
Collaboration proposals'], 'image' => 'images/agriculture.webp', 'alt' => ['ar' => 'الإنتاج الزراعي والحيواني', 'en' => 'Agriculture & livestock production'], 'status' => 'published', 'sort_order' => 6]);
        Sector::firstOrCreate(['slug' => 'marine-services'], ['title' => ['ar' => 'الخدمات البحرية', 'en' => 'Marine services'], 'excerpt' => ['ar' => 'صلة متكاملة بالقطاع البحري.', 'en' => 'Connected to maritime business.'], 'body' => ['ar' => 'الخدمات البحرية ضمن الأنشطة الواردة في نطاق عمل الشركة. تواصل معنا لعرض متطلباتك البحرية ومناقشة نطاق الطلب.', 'en' => 'Marine services form part of the company’s business scope. Contact us to describe your maritime requirements and discuss the scope of your inquiry.'], 'services' => ['ar' => 'متطلبات الخدمات البحرية
استفسارات الأعمال
مقترحات التعاون', 'en' => 'Marine service requirements
Business inquiries
Collaboration proposals'], 'image' => 'images/sea.webp', 'alt' => ['ar' => 'الخدمات البحرية', 'en' => 'Marine services'], 'status' => 'published', 'sort_order' => 7]);
        Sector::firstOrCreate(['slug' => 'import-export'], ['title' => ['ar' => 'الاستيراد والتصدير', 'en' => 'Import & export'], 'excerpt' => ['ar' => 'تواصل يفتح آفاق التجارة.', 'en' => 'Connections that open trade horizons.'], 'body' => ['ar' => 'تضم أنشطة مدار العالم الاستيراد والتصدير. شاركنا طبيعة البضائع والأسواق المعنية والمواصفات لبدء مناقشة طلبك.', 'en' => 'Import and export are among the company’s activities. Share the type of goods, relevant markets and specifications to begin discussing your request.'], 'services' => ['ar' => 'استفسارات الاستيراد
استفسارات التصدير
مواصفات البضائع', 'en' => 'Import inquiries
Export inquiries
Goods specifications'], 'image' => 'images/sea.webp', 'alt' => ['ar' => 'الاستيراد والتصدير', 'en' => 'Import & export'], 'status' => 'published', 'sort_order' => 8]);
        Sector::firstOrCreate(['slug' => 'packaging'], ['title' => ['ar' => 'التعبئة والتغليف', 'en' => 'Packaging'], 'excerpt' => ['ar' => 'اهتمام بالتفاصيل، من المنتج إلى العبوة.', 'en' => 'Attention to detail, from product to package.'], 'body' => ['ar' => 'التعبئة والتغليف من مجالات عمل الشركة. نرحب باستفساراتكم المتعلقة بطبيعة المنتجات والكميات ومتطلبات التعبئة والتغليف.', 'en' => 'Packaging is one of the company’s business areas. We welcome inquiries about product types, quantities and packaging requirements.'], 'services' => ['ar' => 'متطلبات التعبئة
متطلبات التغليف
مواصفات المنتجات والكميات', 'en' => 'Filling requirements
Packaging requirements
Product specifications and quantities'], 'image' => 'images/agriculture.webp', 'alt' => ['ar' => 'التعبئة والتغليف', 'en' => 'Packaging'], 'status' => 'published', 'sort_order' => 9]);
        Page::firstOrCreate(['slug' => 'home'], ['title' => ['ar' => 'مدار يربط الأعمال بالعالم', 'en' => 'Connecting business to the world'], 'body' => ['ar' => 'نجمع مجالات الشحن والاستثمار والإنتاج ضمن منظومة أعمال واحدة. من بغداد، نربط الخبرة المحلية بالتطلعات الأوسع، ونفتح مسارات جديدة للتعاون والنمو.', 'en' => 'We bring freight, investment and production together within one business portfolio. From Baghdad, we connect local expertise with broader ambitions and open paths for collaboration and growth.'], 'seo_description' => ['ar' => 'نجمع مجالات الشحن والاستثمار والإنتاج ضمن منظومة أعمال واحدة. من بغداد، نربط الخبرة المحلية بالتطلعات الأوسع، ونفتح مسارات جديدة للتعاون والنمو.', 'en' => 'We bring freight, investment and production together within one business portfolio. From Baghdad, we connect local expertise with broader ambitions and open paths for collaboration and growth.'], 'status' => 'published']);
        Page::firstOrCreate(['slug' => 'about'], ['title' => ['ar' => 'أعمال تجمعها رؤية واحدة.', 'en' => 'Diverse businesses. One vision.'], 'body' => ['ar' => 'نجمع مجالات الشحن والاستثمار والإنتاج ضمن منظومة أعمال واحدة. من بغداد، نربط الخبرة المحلية بالتطلعات الأوسع، ونفتح مسارات جديدة للتعاون والنمو.', 'en' => 'We bring freight, investment and production together within one business portfolio. From Baghdad, we connect local expertise with broader ambitions and open paths for collaboration and growth.'], 'seo_description' => ['ar' => 'نجمع مجالات الشحن والاستثمار والإنتاج ضمن منظومة أعمال واحدة. من بغداد، نربط الخبرة المحلية بالتطلعات الأوسع، ونفتح مسارات جديدة للتعاون والنمو.', 'en' => 'We bring freight, investment and production together within one business portfolio. From Baghdad, we connect local expertise with broader ambitions and open paths for collaboration and growth.'], 'status' => 'published']);
        Page::firstOrCreate(['slug' => 'sectors'], ['title' => ['ar' => 'تنوّع في الأعمال،
تكامل في الرؤية.', 'en' => 'Diverse capabilities.
A connected vision.'], 'body' => ['ar' => 'من حركة البضائع إلى تطوير المشاريع والإنتاج؛ اكتشف مجالات أعمالنا واختر ما يناسب احتياجاتك.', 'en' => 'From moving goods to developing projects and production, explore the areas of our business and find the right conversation for your needs.'], 'seo_description' => ['ar' => 'من حركة البضائع إلى تطوير المشاريع والإنتاج؛ اكتشف مجالات أعمالنا واختر ما يناسب احتياجاتك.', 'en' => 'From moving goods to developing projects and production, explore the areas of our business and find the right conversation for your needs.'], 'status' => 'published']);
        Page::firstOrCreate(['slug' => 'contact'], ['title' => ['ar' => 'مساحة للتواصل.
وبداية للتعاون.', 'en' => 'A space to connect.
A place to begin.'], 'body' => ['ar' => 'يسعدنا معرفة المزيد عن احتياجاتك. اختر القطاع المناسب وأرسل لنا تفاصيل طلبك.', 'en' => 'Tell us more about your business needs. Choose a sector and send us the details of your request.'], 'seo_description' => ['ar' => 'يسعدنا معرفة المزيد عن احتياجاتك. اختر القطاع المناسب وأرسل لنا تفاصيل طلبك.', 'en' => 'Tell us more about your business needs. Choose a sector and send us the details of your request.'], 'status' => 'published']);
        Page::firstOrCreate(['slug' => 'quote'], ['title' => ['ar' => 'أخبرنا بما تحتاجه.', 'en' => 'Tell us what you need.'], 'body' => ['ar' => 'كل طلب يبدأ بفهم التفاصيل. أكمل النموذج وسجّل احتياجاتك لدى فريق الشركة.', 'en' => 'Every request starts with understanding the details. Complete the form to register your requirements with our team.'], 'seo_description' => ['ar' => 'كل طلب يبدأ بفهم التفاصيل. أكمل النموذج وسجّل احتياجاتك لدى فريق الشركة.', 'en' => 'Every request starts with understanding the details. Complete the form to register your requirements with our team.'], 'status' => 'published']);
        Page::firstOrCreate(['slug' => 'privacy'], ['title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy policy'], 'body' => ['ar' => 'مسودة للمراجعة قبل النشر: تُجمع بيانات الاسم ووسيلة التواصل وتفاصيل الطلب للرد على الاستفسارات. يجب اعتماد مدة الاحتفاظ، والأساس القانوني، وحقوق أصحاب البيانات، ومزودي المعالجة، وطريقة طلب الحذف من الإدارة قبل نشر هذه السياسة.', 'en' => 'Draft for review before publication: name, contact details and request information are collected to respond to inquiries. Management must approve retention periods, legal basis, data subject rights, processors and the deletion request procedure before publishing this policy.'], 'seo_description' => ['ar' => 'مسودة للمراجعة قبل النشر: تُجمع بيانات الاسم ووسيلة التواصل وتفاصيل الطلب للرد على الاستفسارات. يجب اعتماد مدة الاحتفاظ، والأساس القانوني، وحقوق أصحاب البيانات، ومزودي المعالجة، وطريقة طلب الحذف من ال', 'en' => 'Draft for review before publication: name, contact details and request information are collected to respond to inquiries. Management must approve retention periods, legal basis, data subject rights, p'], 'status' => 'draft']);
        Slide::firstOrCreate(['slug' => 'air'], ['title' => ['ar' => 'الشحن الجوي…
اتصال يتجاوز المسافات', 'en' => 'Air freight.
Beyond distance.'], 'excerpt' => ['ar' => 'نربط تطلعات أعمالك بآفاق أوسع، عبر أحد أهم مجالاتنا.', 'en' => 'Connect your business ambitions to broader horizons.'], 'button' => ['ar' => 'استكشف الشحن الجوي', 'en' => 'Explore air freight'], 'alt' => ['ar' => 'طائرة شحن وعمليات مناولة في مطار', 'en' => 'Cargo aircraft and ground handling at an airport'], 'image' => 'images/air.webp', 'link' => '/sectors/air-freight', 'status' => 'published', 'sort_order' => 0]);
        Slide::firstOrCreate(['slug' => 'sea'], ['title' => ['ar' => 'الشحن والخدمات البحرية…
آفاق متصلة', 'en' => 'Sea & marine services.
Connected horizons.'], 'excerpt' => ['ar' => 'حركة التجارة تبدأ باتصال. اكتشف أعمالنا في القطاع البحري.', 'en' => 'Trade starts with a connection. Explore our maritime business.'], 'button' => ['ar' => 'استكشف أعمالنا البحرية', 'en' => 'Explore maritime business'], 'alt' => ['ar' => 'سفينة حاويات ورافعات الميناء', 'en' => 'Container ship and port cranes'], 'image' => 'images/sea.webp', 'link' => '/sectors/sea-freight', 'status' => 'published', 'sort_order' => 1]);
        Slide::firstOrCreate(['slug' => 'industry'], ['title' => ['ar' => 'المقاولات والاستثمارات…
آفاق للبناء والنمو', 'en' => 'Contracting & investment.
Room to grow.'], 'excerpt' => ['ar' => 'مجالات مترابطة، تجمع فرص البناء والتطوير والاستثمار.', 'en' => 'Connected disciplines for development and investment.'], 'button' => ['ar' => 'استكشف قطاعات الاستثمار', 'en' => 'Explore investment sectors'], 'alt' => ['ar' => 'هيكل منشأة صناعية قيد الإنشاء', 'en' => 'Industrial building under construction'], 'image' => 'images/industry.webp', 'link' => '/sectors/industrial-investment', 'status' => 'published', 'sort_order' => 2]);
        Slide::firstOrCreate(['slug' => 'agriculture'], ['title' => ['ar' => 'من الإنتاج
إلى التجهيز والتعبئة', 'en' => 'From production
to supply and packaging.'], 'excerpt' => ['ar' => 'مجالات أعمال تتكامل من الإنتاج الزراعي والحيواني إلى التعبئة والتغليف.', 'en' => 'Connected activities from agricultural production to packaging.'], 'button' => ['ar' => 'اكتشف مجالات أعمالنا', 'en' => 'Discover our sectors'], 'alt' => ['ar' => 'حقول زراعية ومنشأة لتجهيز المنتجات', 'en' => 'Agricultural fields and a produce processing building'], 'image' => 'images/agriculture.webp', 'link' => '/sectors', 'status' => 'published', 'sort_order' => 3]);
        if (! Setting::where('key', 'live_slides')->exists()) {
            Setting::put('live_slides', Slide::orderBy('sort_order')->get()->map(fn ($s) => $s->getAttributesForSnapshot())->all());
        }
        Setting::firstOrCreate(['key' => 'contact'], ['value' => ['email' => 'info@madaralalam.com', 'address' => ['ar' => 'العراق، بغداد، الداوودي', 'en' => 'Iraq, Baghdad, Al Dawoodi']]]);
        Setting::firstOrCreate(['key' => 'brand'], ['value' => ['english_legal_name' => 'MADAR AL ALAM CO.', 'english_approved' => false]]);
        Setting::firstOrCreate(['key' => 'navigation'], ['value' => collect(['home', 'about', 'sectors', 'projects', 'news', 'contact', 'profile'])->mapWithKeys(fn ($v, $i) => [$v => ['visible' => true, 'order' => $i]])->all()]);
    }
}
