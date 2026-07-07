<template>
  <Head title="Shop" />

  <div class="min-h-screen bg-gray-50 text-gray-800 font-sans text-[13px]">

    <!-- ===== Top utility bar ===== -->
    <div class="bg-[#f4f5f7] border-b border-gray-200 hidden md:block">
      <div class="max-w-7xl mx-auto px-4 py-2 flex justify-between items-center text-[12px] text-gray-500">
        <div class="flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a9 9 0 0118 0v6M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3v5zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3v5z" />
          </svg>
          <span class="text-black font-bold">Hotline: 16297 <span class="text-gray-500">(9.00 AM to 11.00 PM)</span></span>
        </div>
        <div class="flex items-center gap-3 text-black-1100 text-[14px]">
          <a href="#" class="hover:text-blue-600">অর্ডার ট্র্যাক করুন</a>
          <span class="text-gray-300">|</span>
          <a href="#" class="hover:text-blue-600">রকমারি উদ্যোক্তা</a>
          <span class="text-gray-300">|</span>
          <a href="#" class="hover:text-blue-600">ঘরে বসে আয় করুন</a>
          <span class="text-gray-300">|</span>
          <a href="#" class="hover:text-blue-600">বই ডোনেশন</a>
          <span class="text-gray-300">|</span>
          <a href="#" class="hover:text-blue-600">রকমারি স্টোর</a>
        </div>
      </div>
    </div>

    <!-- ===== Header ===== -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-6">
        <img
          src="https://www.rokomari.com/static/200/images/rokomari_logo.png"
          alt="Rokomari"
          class="h-10 w-auto shrink-0"
        />

        <div class="flex-1 relative max-w-2xl">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search by superstores (Umbrella, Fan, ...)"
            class="w-full border border-blue-300 rounded-full pl-4 pr-11 py-2 text-[13px] focus:outline-none focus:ring-1 focus:ring-blue-400"
          />
          <button class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>

        <div class="hidden md:flex items-center gap-6 text-[13px] shrink-0 ml-auto">
          <div class="flex items-center gap-2 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <rect x="7" y="2" width="10" height="20" rx="2" />
              <line x1="7" y1="18" x2="17" y2="18" />
            </svg>
            <div class="leading-tight">
              <p class="text-gray-400 text-[11px]">Download</p>
              <p class="font-medium text-gray-800">Rokomari App</p>
            </div>
          </div>
          <div class="flex items-center gap-2 cursor-pointer text-gray-700 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="8" r="3.2" />
              <path stroke-linecap="round" d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6" />
            </svg>
            <span>Hello, Sign in</span>
          </div>
          <div class="relative cursor-pointer text-gray-700 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17M17 19a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 100 4 2 2 0 000-4z" />
            </svg>
            <span
              v-if="cartCount"
              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-[9px] w-4 h-4 flex items-center justify-center"
            >{{ cartCount }}</span>
          </div>
        </div>
      </div>

      <!-- ===== Primary nav row ===== -->
      <nav class="hidden md:block bg-white">
        <div class="max-w-7xl mx-auto px-4 pb-2.5 flex justify-center items-center gap-6 text-[16px] text-gray-800 overflow-x-auto whitespace-nowrap">
          <a v-for="item in navLinks" :key="item" href="#" class="hover:text-blue-600">{{ item }}</a>
          <a href="#" class="text-gray-800 flex items-center gap-1">
            Just for you <span class="text-yellow-400">✨</span>
          </a>
        </div>
      </nav>

      <!-- ===== Secondary nav row (with dropdowns) ===== -->
      <nav class="border-t border-gray-200 hidden md:block bg-white">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex justify-center items-center gap-8 text-[15px] text-gray-700 overflow-x-auto whitespace-nowrap">
          <a v-for="item in secondaryNavLinks" :key="item.label" href="#" class="hover:text-blue-600 flex items-center gap-0.5">
            {{ item.label }}
            <svg v-if="item.dropdown" class="h-3 w-3 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg>
          </a>
        </div>
      </nav>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-4 space-y-5">

   <!-- ===== Hero banner + side ad (same height, side by side) ===== -->
<section class="grid grid-cols-1 md:grid-cols-4 gap-3">
  <div class="md:col-span-3 relative rounded-lg overflow-hidden h-64 bg-white border border-gray-100">
    <transition name="fade" mode="out-in">
      <img :key="activeSlide" :src="heroSlides[activeSlide]?.image" class="w-full h-full object-cover" />
    </transition>
    <button @click="prevSlide" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 rounded-full w-8 h-8 flex items-center justify-center text-gray-600 z-10 shadow">‹</button>
    <button @click="nextSlide" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/90 rounded-full w-8 h-8 flex items-center justify-center text-gray-600 z-10 shadow">›</button>
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
      <span
        v-for="(s, i) in heroSlides" :key="i" @click="goToSlide(i)"
        :class="['w-1.5 h-1.5 rounded-full cursor-pointer', i === activeSlide ? 'bg-gray-500' : 'bg-gray-300']"
      ></span>
    </div>
  </div>

  <div class="hidden md:flex flex-col items-center justify-between rounded-lg overflow-hidden border border-gray-200 bg-linear-to-b from-indigo-500 to-indigo-600 text-white p-4 text-center h-64">
    <div>
      <p class="text-[13px] font-semibold leading-snug">{{ sideAd.line1 }}</p>
      <p class="text-[13px] font-semibold leading-snug">{{ sideAd.line2 }}</p>
      <p class="text-[11px] mt-2 text-indigo-100">{{ sideAd.promo }}</p>
    </div>
    <img :src="sideAd.qrImage" class="w-20 h-20 my-2 rounded bg-white p-1" />
    <div class="flex flex-col gap-1.5 w-full">
      <img :src="sideAd.appStoreImage" class="w-full rounded" />
      <img :src="sideAd.playStoreImage" class="w-full rounded" />
    </div>
    <p class="text-[10px] text-indigo-100 mt-1">{{ sideAd.footerText }}</p>
  </div>
</section>

<!-- ===== Shop by Category ===== -->
<section class="bg-[#F6F6FF] border border-[#ECECF6] rounded-md p-3" >
  <div class="mb-3">
    <h2 class=" pl-5 p-2 text-2xl font-bold text-gray-800">Shop By Category</h2>
  </div>
  <div class="relative pt-2 pl-6 pr-6 pb-5">
    <div class="grid grid-cols-5 sm:grid-cols-10 gap-3 ">
      <div v-for="cat in categories" :key="cat.name" class="flex flex-col items-center text-center gap-2 cursor-pointer group">
        <div class="w-full aspect-square rounded-lg border border-gray-200 flex items-center justify-center bg-white overflow-hidden group-hover:border-blue-400 transition p-3">
          <img :src="cat.icon" class="w-full h-full object-cover" />
        </div>
        <span class="text-[11px] text-gray-700 leading-tight">{{ cat.name }}</span>
      </div>
    </div>
    <button class="hidden lg:flex absolute -right-4 top-1/3 -translate-y-1/2 bg-white border border-gray-200 rounded-full w-8 h-8 items-center justify-center text-gray-600 shadow">›</button>
  </div>
</section>

      <!-- ===== Quick Deal ===== -->
      <section class="border border-blue-100 rounded-md overflow-hidden">
        <div class="bg-[#1a73e8] text-white flex items-center justify-between px-4 py-2">
          <h2 class="font-semibold flex items-center gap-1 text-sm">Quick Deal <span>⚡</span></h2>
        </div>
        <div class="flex gap-5 px-4 pt-2.5 text-[12.5px] border-b border-gray-100 bg-white">
          <button
            v-for="tab in dealTabs" :key="tab" @click="activeDealTab = tab"
            :class="['pb-2 border-b-2', activeDealTab === tab ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-500']"
          >{{ tab }}</button>
        </div>
        <ProductRow :products="quickDeals" showSold />
      </section>

      <!-- =====  আপনার জন্য (For you) ===== -->
      <section class="bg-[#fdfbe8] border border-yellow-100 rounded-md p-3">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-semibold text-sm">শুধু আপনার জন্য</h2>
          <a href="#" class="text-[11px] text-blue-600 border border-blue-200 rounded px-2 py-1 bg-white">View All</a>
        </div>
        <ProductRow :products="forYou" />
      </section>

      <!-- ===== বইমেলা banner ===== -->
      <section class="bg-white border border-gray-100 rounded-md p-3 flex items-start gap-3">
        <span class="text-2xl">🎪</span>
        <p class="text-[11.5px] text-gray-500 leading-relaxed">
          আসছে বইমেলা! সেরা বই খুঁজে নিতে চোখ রাখুন রকমারি বেস্টসেলার অ্যাওয়ার্ড ২০২৬ লিস্টে। এপ্রিল ২০২৬ থেকে মার্চ
          ২০২৬ পর্যন্ত প্রকাশিত বইগুলোর মধ্য থেকে সেরা বইগুলো বেছে নেওয়া হবে এই তালিকার জন্য।
        </p>
      </section>

      <!-- ===== Ranking panels ===== -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <RankingPanel
          v-for="panel in rankingPanels" :key="panel.title"
          :title="panel.title" :color="panel.color" :items="panel.items"
        />
      </section>

      <!-- ===== Recently Sold Products ===== -->
      <SectionHeader title="Recently Sold Products" />
      <ProductRow :products="recentlySold" showDiscountRibbon />

      <!-- ===== Discount books strip ===== -->
      <SectionHeader title="বই কেনার সাহি সুযোগ 🎉 শায়েস্তা খ অফারে ৮০% পর্যন্ত ছাড় 🔥" />
      <ProductRow :products="discountBooks" showDiscountRibbon />

      <!-- ===== Explore Kids' Products ===== -->
      <SectionHeader title="Explore Our Kids' Products" />
      <div class="grid grid-cols-3 sm:grid-cols-6 gap-4">
        <div v-for="kid in kidsCategories" :key="kid.name" class="flex flex-col items-center gap-2">
          <div class="w-16 h-16 rounded-full overflow-hidden border border-gray-200 bg-white">
            <img :src="kid.img" class="w-full h-full object-cover" />
          </div>
          <span class="text-[11px] text-center text-gray-600">{{ kid.name }} ›</span>
        </div>
      </div>

      <!-- ===== Promo banners strip ===== -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div v-for="promo in promoStrip" :key="promo.text" class="flex items-center gap-2 border border-gray-100 rounded-md p-2 bg-white">
          <span class="text-xl">{{ promo.icon }}</span>
          <span class="text-[11.5px] text-gray-600">{{ promo.text }}</span>
        </div>
      </section>

      <!-- ===== Newly Released Products ===== -->
      <SectionHeader title="Newly Released Products" />
      <ProductRow :products="newlyReleased" />

      <!-- ===== Best Selling Ebooks ===== -->
      <SectionHeader title="Best Selling Ebooks" />
      <ProductRow :products="ebooks" badge="eBook" />

      <!-- ===== Buy Books of Top Authors ===== -->
      <SectionHeader title="Buy Books of Top Authors" />
      <div class="flex gap-6 overflow-x-auto pb-2">
        <div v-for="author in authors" :key="author.name" class="flex flex-col items-center gap-2 shrink-0">
          <img :src="author.img" class="w-16 h-16 rounded-full object-cover border border-gray-200" />
          <span class="text-[11px] text-center w-20 leading-tight">{{ author.name }}</span>
        </div>
      </div>

      <!-- ===== Shop From Top Brands ===== -->
      <section class="border border-blue-100 rounded-md overflow-hidden">
        <div class="bg-[#1a73e8] text-white px-4 py-2 font-semibold text-sm">Shop From Top Brands</div>
        <div class="flex gap-5 px-4 pt-2.5 text-[12.5px] bg-white border-b border-gray-100">
          <button
            v-for="tab in brandTabs" :key="tab" @click="activeBrandTab = tab"
            :class="['pb-2 border-b-2', activeBrandTab === tab ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-500']"
          >{{ tab }}</button>
        </div>
        <div class="flex gap-5 overflow-x-auto px-4 py-3 bg-white">
          <div v-for="brand in brands" :key="brand" class="w-20 h-20 rounded-full border border-gray-200 flex items-center justify-center shrink-0 bg-white">
            <span class="text-[10.5px] font-semibold text-gray-600 text-center px-1">{{ brand }}</span>
          </div>
        </div>
      </section>

      <!-- ===== Category book grids: Academic / Islamic / Language / Novel ===== -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <BookGrid v-for="grid in bookGrids" :key="grid.title" :title="grid.title" :books="grid.books" />
      </section>

      <!-- ===== Seasonal quick links ===== -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <QuickLinkGroup v-for="group in quickLinkGroups" :key="group.title" :title="group.title" :items="group.items" />
      </section>

    </main>

    <!-- ===== Footer ===== -->
    <footer class="bg-slate-800 text-gray-300 mt-10">
      <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-2 md:grid-cols-5 gap-6 text-sm">
        <div v-for="col in footerColumns" :key="col.title">
          <h3 class="text-white font-semibold mb-3">{{ col.title }}</h3>
          <ul class="space-y-2">
            <li v-for="link in col.links" :key="link"><a href="#" class="hover:text-white">{{ link }}</a></li>
          </ul>
        </div>
      </div>
      <div class="border-t border-slate-700 text-center text-xs py-4 text-gray-400">
        © {{ new Date().getFullYear() }} Rokomari.com — All rights reserved (demo layout, not affiliated)
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, h, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'

/* Replace these with data fetched from your DB/API */
const heroSlides = ref([
  { image: 'https://rokbucket.rokomari.io/banner/DESKTOP41a96783-2eac-4127-8659-f24099487b22.webp' },
  { image: 'https://rokbucket.rokomari.io/banner/DESKTOPe463d458-4443-485e-b9de-384ecb8d0ce2.webp' },
  { image: 'https://rokbucket.rokomari.io/banner/DESKTOPc58adcb4-106c-4273-931c-4678fddb4a58.webp' },
])

const sideAd = ref({
  line1: 'অ্যাপে ১ম অর্ডারে ফ্রি শিপিং',
  line2: '৯৯৯+ টাকা এমাউন্টে',
  promo: 'প্রোমোকোডঃ APP1ST',
  qrImage: 'https://placehold.co/110x110/ffffff/1e293b?text=QR',
  appStoreImage: 'https://placehold.co/160x40/000000/ffffff?text=App+Store',
  playStoreImage: 'https://placehold.co/160x40/000000/ffffff?text=Google+Play',
  footerText: 'Scan the QR code to Download App',
})

const activeSlide = ref(0)
let autoplayTimer = null

function nextSlide() {
  activeSlide.value = (activeSlide.value + 1) % heroSlides.value.length
}
function prevSlide() {
  activeSlide.value = (activeSlide.value - 1 + heroSlides.value.length) % heroSlides.value.length
}
function goToSlide(i) {
  activeSlide.value = i
}
function startAutoplay() {
  stopAutoplay()
  autoplayTimer = setInterval(nextSlide, 4000)
}
function stopAutoplay() {
  if (autoplayTimer) clearInterval(autoplayTimer)
}

onMounted(startAutoplay)
onUnmounted(stopAutoplay)

/* ---------------- State ---------------- */
const searchQuery = ref('')
const cartCount = ref(0)
const activeDealTab = ref("Today's Deal")
const activeBrandTab = ref('Just For You')

/* ---------------- Static data ---------------- */
const navLinks = ['বই', 'ই-বুক', 'ইলেক্ট্রনিক্স', 'সুপার স্টোর', 'কিডস জোন', 'Bulk Order', 'বেস্টসেলার অ্যাওয়ার্ড, ২৫', 'আজকের অফার !', 'বইমেলা ২০২৬']

const secondaryNavLinks = [
  { label: 'লেখক', dropdown: true },
  { label: 'বিষয়', dropdown: true },
  { label: 'প্রকাশনী', dropdown: true },
  { label: 'বইমেলা ২০২৬', dropdown: false },
  { label: 'একাডেমিক বই', dropdown: false },
  { label: 'অতিরিক্ত ছাড়ের বই', dropdown: false },
  { label: 'প্যারালাল TEXT', dropdown: true },
  { label: 'ভর্তি প্রস্তুতি', dropdown: true },
  { label: 'ইসলামি বই', dropdown: false },
  { label: 'ইংরেজি ভাষার বই', dropdown: false },
  { label: 'পশ্চিমবঙ্গের বই', dropdown: false },
]

const categories = [
  { name: 'বই', icon: 'https://rokbucket.rokomari.io/category/d736a877ccc34_image.png' },
  { name: 'Electronics', icon: 'https://rokbucket.rokomari.io/category/8c2dc7dc74204_image.png' },
  { name: 'Ebook', icon: 'https://rokbucket.rokomari.io/category/5bc79e3cefc14_image.png' },
  { name: 'Beauty & Health', icon: 'https://rokbucket.rokomari.io/category/d025ff454b524_image.png' },
  { name: 'Stationery', icon: 'https://rokbucket.rokomari.io/category/97a67eaeaf404_image.png' },
  { name: 'Science Kit', icon: 'https://rokbucket.rokomari.io/category/28ddb20e3c594_image.png' },
  { name: 'Groceries', icon: 'https://rokbucket.rokomari.io/category/cd24452cc0404_image.png' },
  { name: 'Gift Voucher', icon: 'https://rokbucket.rokomari.io/category/edd08a66c8424_image.png' },
  { name: 'Islamic Accessories', icon: 'https://rokbucket.rokomari.io/category/0737f0c5b80e4_image.png' },
  { name: 'Mother, Baby & Kids', icon: 'https://rokbucket.rokomari.io/category/4573477800844_image.png' },
]

function makeProducts(n, prefix, opts = {}) {
  return Array.from({ length: n }, (_, i) => ({
    id: `${prefix}-${i}`,
    title: `${prefix} product name goes here ${i + 1}`,
    brand: opts.brand || 'Non-Brand',
    price: 149 + i * 40,
    oldPrice: 250 + i * 60,
    discount: 10 + (i % 4) * 10,
    rating: 4,
    reviews: 5 + i,
    sold: 20 + i * 30,
    inStock: true,
    img: `https://placehold.co/200x200/e9edf3/64748b?text=${encodeURIComponent(prefix + ' ' + (i + 1))}`,
  }))
}

const quickDeals = makeProducts(7, 'Deal')
const forYou = makeProducts(6, 'For You')
const ebooks = makeProducts(7, 'Ebook')
const recentlySold = makeProducts(6, 'Sold')
const discountBooks = makeProducts(6, 'Book')
const newlyReleased = makeProducts(6, 'New')

const dealTabs = ["Today's Deal", 'Buy 1 Get 1', 'Free Shipping', 'Super Saving Bundle']
const brandTabs = ['Just For You', 'Electronics', 'Superstore']
const brands = ['CASIO', 'রকমারি', 'Karkuma', 'PILOT', 'Cetaphil', 'CeraVe', 'HEART', 'mamaearth']

const authors = Array.from({ length: 8 }, (_, i) => ({
  name: `Author Name ${i + 1}`,
  img: `https://placehold.co/64/94a3b8/ffffff?text=A${i + 1}`,
}))

const rankingPanels = [
  {
    title: 'ফিকশন বই',
    color: 'blue',
    items: [
      { rank: 1, title: 'নিঃসঙ্গ', sub: 'হুমায়ূন আহমেদ', reviews: 67, progress: 90 },
      { rank: 2, title: 'শঙ্খচিল রাত্রি', sub: 'জাফর ইকবাল', reviews: 25, progress: 70 },
      { rank: 3, title: 'বিসর্জনের গল্প', sub: 'মহাশ্বেতা দেবী', reviews: 133, progress: 55 },
    ],
  },
  {
    title: 'নন ফিকশন বই',
    color: 'purple',
    items: [
      { rank: 1, title: 'মানবিক সাধনা', sub: 'হুমায়ূন আজাদ', reviews: 77, progress: 88 },
      { rank: 2, title: 'Plants of Bangladesh', sub: 'বৈজ্ঞানিক প্রকাশনা', reviews: 2, progress: 60 },
      { rank: 3, title: 'শরণার্থীর বয়ান', sub: 'সাহিত্য একাদেমি', reviews: 14, progress: 45 },
    ],
  },
  {
    title: 'ক্যারিয়ার ও একাডেমিক বই',
    color: 'green',
    items: [
      { rank: 1, title: 'বিসিএস এইচএসসি স্পেশাল', sub: 'প্রফেসর প্রকাশনী', reviews: 209, progress: 95 },
      { rank: 2, title: '১৫ তম শিক্ষক নিবন্ধন', sub: 'একাডেমিক পাব্লিকেশন', reviews: 68, progress: 65 },
      { rank: 3, title: 'বিসিএস প্রিলি বাংলা', sub: 'সাহিত্য পাব্লিকেশন', reviews: 235, progress: 50 },
    ],
  },
]

const bookGrids = [
  { title: 'একাডেমিক বই', books: makeProducts(4, 'Academic') },
  { title: 'ইসলামি বই', books: makeProducts(4, 'Islamic') },
  { title: 'ভাষা ও অভিধান বই', books: makeProducts(4, 'Language') },
  { title: 'উপন্যাসের বই', books: makeProducts(4, 'Novel') },
]

const quickLinkGroups = [
  { title: 'Stay Cool in Summer', items: [ { name: 'Fan', icon: '🌀' }, { name: 'Umbrella', icon: '☂️' }, { name: 'AC', icon: '❄️' }, { name: 'Anti Mosquito', icon: '🦟' } ] },
  { title: 'Beauty & Health Products', items: [ { name: 'Personal Care', icon: '🧴' }, { name: 'Beauty Tools', icon: '💅' }, { name: 'Shaving & Grooming', icon: '🪒' }, { name: 'Skin Care', icon: '🧼' } ] },
  { title: 'Eid Fest', items: [ { name: 'Fashion', icon: '👗' }, { name: 'Spices', icon: '🌶️' }, { name: 'Islamic Accessories', icon: '🕌' }, { name: 'Perfume', icon: '🧴' } ] },
  { title: 'Gear and Gadgets', items: [ { name: 'Mobile Accessories', icon: '📱' }, { name: 'Wearable Technology', icon: '⌚' }, { name: 'Headphone', icon: '🎧' }, { name: 'Shaving & Grooming', icon: '🪒' } ] },
]

const kidsCategories = [
  { name: 'Baby Body Wash', img: 'https://placehold.co/64/fde68a/1e293b?text=🧴' },
  { name: 'Lotions & Creams', img: 'https://placehold.co/64/fbcfe8/1e293b?text=🧴' },
  { name: "Kids' Toys", img: 'https://placehold.co/64/bfdbfe/1e293b?text=🧸' },
  { name: 'Strollers & Prams', img: 'https://placehold.co/64/ddd6fe/1e293b?text=🚼' },
  { name: 'Baby Feeding', img: 'https://placehold.co/64/bbf7d0/1e293b?text=🍼' },
  { name: 'Baby Powders', img: 'https://placehold.co/64/fed7aa/1e293b?text=🧴' },
]

const promoStrip = [
  { icon: '💰', text: 'বই হাতে পেয়ে মূল্য পরিশোধ করুন' },
  { icon: '📖', text: 'অর্ডার করার আগে একটু পড়ে দেখুন' },
  { icon: '🏅', text: 'অর্ডার করলেই থাকছে পয়েন্ট' },
]

const footerColumns = [
  { title: 'Company', links: ['About Us', 'Careers', 'Contact', 'Blog'] },
  { title: 'Help', links: ['FAQ', 'Return Policy', 'Track Order', 'Support'] },
  { title: 'Categories', links: ['Books', 'Electronics', 'Beauty', 'Stationery'] },
  { title: 'Policies', links: ['Terms', 'Privacy', 'Refund', 'Shipping'] },
  { title: 'Connect', links: ['Facebook', 'Instagram', 'YouTube', 'App Download'] },
]

/* ---------------- Local sub-components ---------------- */
const SectionHeader = {
  props: { title: String },
  setup(props) {
    return () =>
      h('div', { class: 'flex items-center justify-between' }, [
        h('h2', { class: 'font-semibold text-gray-800 text-sm' }, props.title),
        h('a', { href: '#', class: 'text-[11px] text-blue-600 border border-blue-200 rounded px-2 py-1' }, 'View All'),
      ])
  },
}

const ProductRow = {
  props: { products: Array, badge: String, showSold: Boolean, showDiscountRibbon: Boolean },
  setup(props) {
    return () =>
      h(
        'div',
        { class: 'flex gap-3 overflow-x-auto pb-2' },
        props.products.map((p) =>
          h('div', { key: p.id, class: 'w-40 shrink-0 border border-gray-100 rounded-md p-2 relative bg-white hover:shadow-md transition' }, [
            p.discount
              ? h('span', {
                  class: 'absolute top-1 left-1 z-10 bg-red-500 text-white text-[10px] font-bold rounded-full w-9 h-9 flex flex-col items-center justify-center leading-none shadow',
                }, [h('span', {}, `${p.discount}%`), h('span', { class: 'text-[7px]' }, 'OFF')])
              : null,
            props.badge
              ? h('span', { class: 'absolute top-0 right-0 z-10 bg-blue-600 text-white text-[8.5px] px-1.5 py-0.5 rounded-bl' }, props.badge)
              : null,
            h('img', { src: p.img, class: 'w-full h-28 object-contain mb-2' }),
            h('p', { class: 'text-[11.5px] line-clamp-2 h-8 leading-tight' }, p.title),
            h('p', { class: 'text-[10.5px] text-gray-400' }, p.brand),
            h('div', { class: 'flex items-center gap-1 text-[10.5px] text-yellow-500' }, [
              '★'.repeat(p.rating),
              h('span', { class: 'text-gray-400' }, `(${p.reviews})`),
            ]),
            p.inStock ? h('p', { class: 'text-[10px] text-green-600' }, 'Product In Stock') : null,
            h('div', { class: 'flex items-center gap-2 mt-1' }, [
              h('span', { class: 'text-gray-400 line-through text-[11px]' }, `TK ${p.oldPrice}`),
              h('span', { class: 'text-red-600 font-semibold text-[12.5px]' }, `TK ${p.price}`),
            ]),
            props.showSold ? h('p', { class: 'text-[10px] text-gray-400 mt-0.5' }, `${p.sold}+ Sold`) : null,
          ])
        )
      )
  },
}

const rankColors = {
  blue: { header: 'bg-blue-100 text-blue-700', bar: 'bg-blue-500' },
  purple: { header: 'bg-purple-100 text-purple-700', bar: 'bg-purple-500' },
  green: { header: 'bg-green-100 text-green-700', bar: 'bg-green-500' },
}

const RankingPanel = {
  props: { title: String, color: String, items: Array },
  setup(props) {
    return () => {
      const c = rankColors[props.color] || rankColors.blue
      return h('div', { class: 'rounded-md border border-gray-100 overflow-hidden bg-white' }, [
        h('div', { class: `${c.header} px-3 py-2 flex items-center justify-between text-[12.5px] font-semibold` }, [
          h('span', {}, props.title),
          h('div', { class: 'flex gap-1 text-[10px] bg-white/70 rounded-full px-1' }, [
            h('span', { class: 'px-2 py-0.5 rounded-full bg-white font-semibold' }, 'বই'),
            h('span', { class: 'px-2 py-0.5 text-gray-500' }, 'লেখক'),
          ]),
        ]),
        h('div', { class: 'p-3 space-y-3' }, props.items.map((it) =>
          h('div', { key: it.rank, class: 'flex items-center gap-2' }, [
            h('span', { class: 'text-[11px] font-semibold text-gray-400 w-3' }, `${it.rank}`),
            h('div', { class: 'w-9 h-11 bg-gray-100 rounded shrink-0 flex items-center justify-center text-[9px] text-gray-400' }, '📕'),
            h('div', { class: 'flex-1 min-w-0' }, [
              h('p', { class: 'text-[11.5px] truncate' }, it.title),
              h('p', { class: 'text-[10px] text-gray-400 truncate' }, it.sub),
              h('div', { class: 'w-full h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden' }, [
                h('div', { class: `h-full ${c.bar}`, style: `width:${it.progress}%` }),
              ]),
            ]),
            it.rank === 1 ? h('span', { class: 'text-yellow-400 text-sm' }, '👑') : h('span', { class: 'text-gray-300 text-xs' }, '—'),
          ])
        )),
      ])
    }
  },
}

const BookGrid = {
  props: { title: String, books: Array },
  setup(props) {
    return () =>
      h('div', { class: 'border border-gray-200 rounded-md p-3 bg-white' }, [
        h('h3', { class: 'text-[12.5px] font-semibold mb-2' }, props.title),
        h('div', { class: 'grid grid-cols-2 gap-2' }, props.books.map((b) =>
          h('div', { key: b.id, class: 'flex flex-col items-center text-center gap-1' }, [
            h('img', { src: b.img, class: 'w-16 h-20 object-cover rounded' }),
            h('p', { class: 'text-[10.5px] line-clamp-2' }, b.title),
          ])
        )),
        h('a', { href: '#', class: 'text-[11px] text-blue-600 mt-2 inline-block' }, 'See More >'),
      ])
  },
}

const QuickLinkGroup = {
  props: { title: String, items: Array },
  setup(props) {
    return () =>
      h('div', { class: 'border border-gray-200 rounded-md p-3 bg-white' }, [
        h('h3', { class: 'text-[12.5px] font-semibold mb-2' }, props.title),
        h('div', { class: 'grid grid-cols-2 gap-3' }, props.items.map((it) =>
          h('div', { key: it.name, class: 'flex flex-col items-center gap-1 text-[10.5px] text-gray-600' }, [
            h('span', { class: 'text-xl' }, it.icon),
            h('span', {}, it.name),
          ])
        )),
      ])
  },
}
</script>
<!-- .fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; } -->
