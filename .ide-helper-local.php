<?php
namespace  {
    exit("This file should not be included, only analyzed by your IDE");
}

namespace Illuminate\Testing {
    /*
     * Documents the data macro.
     */
    class TestResponse
    {
        /**
         * @param string $key
         * @return \Illuminate\Support\Collection|mixed
         */
        public function data ($key)
        {
            return $key;
        }

        /**
         * @param $name
         * @return void
         */
        public function assertActiveHeaderTab ($name)
        {
        }

        public function assertSeeEncoded (string $value): TestResponse
        {
        }
    }
}

namespace Illuminate\Support {
    class Collection
    {
        /**
         * @param mixed $value
         */
        public function assertContains ($value): void
        {
        }

        /**
         * @param mixed $value
         */
        public function assertNotContains ($value): void
        {
        }
    }
}
