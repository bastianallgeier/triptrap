<template>
  <k-field :label="label" :name="name">
    <k-input class="k-editor" theme="field">
      <editor-menu-bar :editor="editor">
        <nav class="k-toolbar" slot-scope="{ commands, isActive, getMarkAttrs }">
          <div class="k-toolbar-wrapper">
            <div class="k-toolbar-buttons">
              <k-dropdown>
                <k-button @click="$refs.blocks.toggle()" class="k-toolbar-button" :icon="activeBlock.icon">{{ activeBlock.label }} &nbsp;</k-button>
                <k-dropdown-content ref="blocks">
                  <k-dropdown-item icon="text" @click="turnInto('paragraph', null, isActive)">Text</k-dropdown-item>
                  <hr />
                  <k-dropdown-item icon="title" @click="turnInto('heading', { level: 1 })">Heading 1</k-dropdown-item>
                  <k-dropdown-item icon="title" @click="turnInto('heading', { level: 2 })">Heading 2</k-dropdown-item>
                  <k-dropdown-item icon="title" @click="turnInto('heading', { level: 3 })">Heading 3</k-dropdown-item>
                  <hr />
                  <k-dropdown-item icon="list-bullet" @click="turnInto('bullet_list')">Bulleted List</k-dropdown-item>
                  <k-dropdown-item icon="list-numbers" @click="turnInto('ordered_list')">Numbered List</k-dropdown-item>
                  <hr />
                  <k-dropdown-item icon="code" @click="turnInto('code_block')">Code</k-dropdown-item>
                  <k-dropdown-item icon="quote" @click="turnInto('blockquote')">Quote</k-dropdown-item>
                </k-dropdown-content>
              </k-dropdown>

              <span class="k-toolbar-divider" />

              <k-button :class="{ 'is-active': isActive.bold() }" class="k-toolbar-button" icon="bold" @click="commands.bold" />
              <k-button :class="{ 'is-active': isActive.italic() }" class="k-toolbar-button" icon="italic" @click="commands.italic" />
              <k-button :class="{ 'is-active': isActive.code() }" class="k-toolbar-button" icon="code" @click="commands.code" />

              <span class="k-toolbar-divider" />

              <k-button :class="{ 'is-active': isActive.link() }" class="k-toolbar-button" icon="url" @click="openLinkDialog(getMarkAttrs('link'))" />

            </div>
          </div>
        </nav>
      </editor-menu-bar>
      <editor-content class="k-editor-content" :editor="editor" :spellcheck="false" @click.native="onClick" />

      <k-dialog ref="link" button="Insert" @submit="$refs.linkForm.submit()">
        <k-form ref="linkForm" :fields="$options.linkFields" @submit="insertLink" v-model="linkModel" />
      </k-dialog>

    </k-input>
  </k-field>
</template>

<script>

import { Editor, EditorContent, EditorMenuBar } from 'tiptap';
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  Bold,
  Code,
  Image,
  Italic,
  Link,
  Strike,
  Underline,
  History,
} from 'tiptap-extensions';


export default {
  inheritAttrs: false,
  blocks: {
    text: {
      id: "paragraph",
      label: "Text",
      icon: "text"
    },
    heading1: {
      id: "heading",
      args: { level: 1 },
      label: "Heading 1",
      icon: "title"
    },
    heading2: {
      id: "heading",
      args: { level: 2 },
      label: "Heading 2",
      icon: "title"
    },
    heading3: {
      id: "heading",
      args: { level: 3 },
      label: "Heading 3",
      icon: "title"
    },
    ul: {
      id: "bullet_list",
      label: "Bulleted list",
      icon: "list-bullet"
    },
    ol: {
      id: "ordered_list",
      label: "Numbered list",
      icon: "list-numbers"
    },
    code: {
      id: "code_block",
      label: "Code",
      icon: "code"
    },
    quote: {
      id: "blockquote",
      label: "Quote",
      icon: "quote"
    }
  },
  linkFields: {
    url: {
      label: "Link",
      placeholder: "https://",
      type: "text",
      icon: "url"
    }
  },
  components: {
    EditorContent,
    EditorMenuBar,
  },
  props: {
    label: String,
    name: String,
    value: String
  },
  data() {
    return {
      activeBlock: this.$options.blocks.text,
      linkModel: {
        url: null
      },
      editor: new Editor({
        extensions: [
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3, 4, 5, 6] }),
          new HorizontalRule(),
          new ListItem(),
          new OrderedList(),
          new Bold(),
          new Code(),
          new Image(),
          new Italic(),
          new Link(),
          new Strike(),
          new Underline(),
          new History(),
        ],
        content: this.value
      }),
    }
  },
  computed: {
    html() {
      return this.editor.getHTML();
    },
    json() {
      return this.editor.getJSON();
    }
  },
  watch: {
    "editor.isActive"() {
      this.activeBlock = this.getActiveBlock();
    },
    html(value) {
      this.$emit("input", value);
      this.activeBlock = this.getActiveBlock();
    },
    value(value) {
      if (value !== this.html) {
        this.editor.setContent(value);
      }
    }
  },
  beforeDestroy() {
    this.editor.destroy()
  },
  methods: {
    insertLink(values) {
      const href = values.url.length === 0 ? null : values.url;
      this.editor.commands.link({ href : href });
      this.$refs.link.close();
    },
    openLinkDialog(attrs) {
      this.linkModel.url = attrs.href;
      this.$refs.link.open();
    },
    getActiveBlock() {

      let activeBlock = this.$options.blocks.text;

      Object.values(this.$options.blocks).forEach(block => {
        if (this.editor.isActive[block.id](block.args || {}) === true) {
          activeBlock = block;
        }
      });

      return activeBlock;
    },
    turnInto(block, args) {
      Object.keys(this.editor.isActive).forEach(check => {
        if (this.editor.isActive[check]() === true) {
          if (this.editor.commands[check]) {
            this.editor.commands[check]();
          }
        }
      });

      if (this.editor.commands[block]) {
        this.editor.commands[block](args);
      }
    },
    onClick() {
      this.$refs.blocks.close();
    }
  }
};
</script>

<style lang="scss">

$color-focus: #4271ae;

.k-editor {
  position: relative;
}

.k-editor .k-toolbar {
  height: 37px;
}
.k-editor:focus-within .k-toolbar {
  position: sticky;
  top: 0;
  right: 0;
  left: 0;
  z-index: 1;
  box-shadow: rgba(0, 0, 0, 0.05) 0 2px 5px;
  border-bottom: 1px solid rgba(#000, 0.1);
  color: #000;
}

.k-editor:focus-within .k-toolbar-button.is-active {
  color: $color-focus;
}
.k-editor .k-toolbar-button {
  font-size: .875rem;
  width: auto;
  padding: 0 .5rem;
  display: flex;
  align-items: center;
}

.ProseMirror {
  padding: .5rem;
  background: #fff;
  line-height: 1.5rem;
}
.ProseMirror:focus {
  outline: 0;
}
.ProseMirror > *:last-child {
  margin-bottom: 0;
}
.ProseMirror a {
  color: $color-focus;
  text-decoration: underline;
  pointer-events: none;
}

.ProseMirror h1,
.ProseMirror > p {
  margin-bottom: 1.5rem;
}
.ProseMirror h1 {
  font-size: 1.75rem;
}
.ProseMirror h2 {
  font-size: 1.25rem;
  margin-bottom: .75rem;
}
.ProseMirror h3 {
  font-size: 1rem;
}
.ProseMirror code {
  position: relative;
  font-size: .875rem;
  display: inline-block;
  line-height: 1.325;
  padding: .05em .5em;
  background: rgba(#000, .1);
  border-radius: 3px;
  font-family: SFMono-Regular, Consolas, Liberation Mono, Menlo, Courier, monospace;
}
.ProseMirror pre {
  background: #2d2e36;
  padding: 1rem !important;
  margin: 1.5rem 0;
  border-radius: 3px;
  font-family: SFMono-Regular, Consolas, Liberation Mono, Menlo, Courier, monospace;
}
.ProseMirror pre code {
  background: none;
  line-height: 1.5em;
  color: #fff;
  display: block;
  padding: 0;
}
.ProseMirror ul,
.ProseMirror ol {
  margin-left: 1.25rem;
  margin-bottom: 1.5rem;
}
.ProseMirror ul > li {
  list-style: disc;
}
.ProseMirror ol > li {
  list-style: decimal;
}
.ProseMirror li {
  margin-bottom: 0;
}
.ProseMirror blockquote {
  font-size: 1.25rem;
  line-height: 1.125em !important;
  padding: 0 0 0 1rem !important;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  border-left: 4px solid #000;
}

.ProseMirror hr {
  position: relative;
  border: 0;
  height: 1px;
  color: rgba(0,0,0, .125);
  margin-bottom: 1.5rem;
}
.ProseMirror hr::after {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: currentColor;
  content: "";
}
.ProseMirror hr {
  background: #efefef;
}
.ProseMirror img {
  width: 100%;
  margin: .75rem 0;
}
.ProseMirror.ProseMirror-hideselection .ProseMirror-selectednode {
  outline: 2px solid #b5d7fe;
  outline-offset: 2px;
}
</style>
